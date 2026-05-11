<?php

namespace App\Services\Backoffice;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class MemberService
{
    public static function memberLevelLabels(): array
    {
        return [
            'pending' => '가입대기회원',
            'associate' => '준회원',
            'regular' => '정회원',
            'lifetime' => '평생회원',
            'senior' => '시니어회원',
        ];
    }

    public static function jobTypeLabels(): array
    {
        return [
            'specialist' => '전문의',
            'resident' => '전공의',
            'public_doctor' => '공보의',
            'military_doctor' => '군의관',
            'nurse' => '간호사',
            'other' => '기타',
        ];
    }

    public function getMembers(array $filters = [], int $perPage = 20)
    {
        $query = User::query()
            ->where('role', 'user')
            ->whereNull('withdrawn_at');

        $this->applyMemberListFilters($query, $filters);

        $sort = $filters['sort_order'] ?? 'joinDate';
        $query = match ($sort) {
            'name' => $query->orderBy('name', 'asc'),
            'id' => $query->orderBy('login_id', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  Builder<User>  $query
     */
    private function applyMemberListFilters(Builder $query, array $filters): void
    {
        if (isset($filters['join_type']) && $filters['join_type']) {
            $query->byJoinType($filters['join_type']);
        }

        $dateStart = $filters['date_start'] ?? $filters['join_date_start'] ?? null;
        $dateEnd = $filters['date_end'] ?? $filters['join_date_end'] ?? null;
        $searchCondition = $filters['search_condition'] ?? 'joinDate';
        if ($dateStart || $dateEnd) {
            $query->whereSearchDateRange($searchCondition, $dateStart, $dateEnd);
        }

        if (isset($filters['marketing_consent']) && is_array($filters['marketing_consent']) && count($filters['marketing_consent']) > 0) {
            $query->where(function ($q) use ($filters) {
                if (in_array('email', $filters['marketing_consent'], true)) {
                    $q->orWhere('email_marketing_consent', true);
                }
                if (in_array('kakao', $filters['marketing_consent'], true)) {
                    $q->orWhere('kakao_marketing_consent', true);
                }
            });
        }

        $grades = $filters['grades'] ?? null;
        if (is_array($grades) && $grades !== [] && ! in_array('all', $grades, true)) {
            $query->whereMemberLevels($grades);
        }

        $query->whereCertifiedFilter($filters['is_certified'] ?? 'all');
        $query->whereInactiveDormant((bool) ($filters['inactive_only'] ?? false));
        $query->whereAnnualFeeStatus($filters['annual_fee'] ?? 'all');
        $this->applyExecutiveStatusFilter($query, (string) ($filters['executive_status'] ?? 'all'));
        $query->whereMembershipFeeBasis(
            $filters['due_mode'] ?? 'all',
            $filters['due_date'] ?? null
        );

        $searchField = trim((string) ($filters['search_field'] ?? ''));
        $searchKeyword = trim((string) ($filters['search_keyword'] ?? ''));
        if ($searchField !== '' && $searchKeyword !== '') {
            $query->memberListKeyword($searchField, $searchKeyword);
        } elseif (isset($filters['search_type']) && isset($filters['search_term'])) {
            $query->search($filters['search_type'], $filters['search_term']);
        }
    }

    public function getMember(int $id): User
    {
        return User::query()->where('role', 'user')->findOrFail($id);
    }

    public function createMember(array $data): User
    {
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        }

        if (($data['join_type'] ?? null) === 'email' && isset($data['email']) && trim((string) ($data['login_id'] ?? '')) === '') {
            $data['login_id'] = $data['email'];
        }

        if (! isset($data['terms_agreed_at'])) {
            $data['terms_agreed_at'] = now();
        }

        if (isset($data['phone_number']) && ! str_starts_with((string) $data['phone_number'], 'sns_')) {
            $data['phone_number'] = User::normalizePhone($data['phone_number']);
        }

        if (! isset($data['member_level']) || $data['member_level'] === '') {
            $data['member_level'] = 'pending';
        }

        $data['committee_codes'] = $this->normalizeCommitteeCodes($data['committee_codes'] ?? null);

        $data['role'] = 'user';
        $data['is_active'] = true;
        $data['email_marketing_consent_at'] = ! empty($data['email_marketing_consent']) ? now() : null;
        $data['kakao_marketing_consent_at'] = ! empty($data['kakao_marketing_consent']) ? now() : null;

        return User::create($data);
    }

    public function updateMember(int $id, array $data): User
    {
        $member = $this->getMember($id);

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['password_confirmation']);

        if (isset($data['phone_number']) && ! str_starts_with((string) $data['phone_number'], 'sns_')) {
            $data['phone_number'] = User::normalizePhone($data['phone_number']);
        }

        if (array_key_exists('email_marketing_consent', $data)) {
            $data['email_marketing_consent_at'] = ! empty($data['email_marketing_consent']) ? now() : null;
        }
        if (array_key_exists('kakao_marketing_consent', $data)) {
            $data['kakao_marketing_consent_at'] = ! empty($data['kakao_marketing_consent']) ? now() : null;
        }

        if (array_key_exists('committee_codes', $data)) {
            $data['committee_codes'] = $this->normalizeCommitteeCodes($data['committee_codes']);
        }

        $member->update($data);

        return $member;
    }

    /**
     * @param  mixed  $raw
     * @return list<string>|null
     */
    private function normalizeCommitteeCodes(mixed $raw): ?array
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        if (! is_array($raw)) {
            return null;
        }
        $allowed = array_keys(config('member_committees', []));
        $out = [];
        foreach ($raw as $code) {
            $code = (string) $code;
            if (in_array($code, $allowed, true)) {
                $out[] = $code;
            }
        }

        return $out === [] ? null : array_values(array_unique($out));
    }

    public function deleteMember(int $id): User
    {
        $member = $this->getMember($id);
        $member->update(['withdrawn_at' => now()]);

        return $member;
    }

    public function deleteMembers(array $ids): int
    {
        return User::query()->whereKey($ids)
            ->where('role', 'user')
            ->whereNull('withdrawn_at')
            ->update(['withdrawn_at' => now()]);
    }

    public function getWithdrawnMembers(array $filters = [], int $perPage = 20)
    {
        $query = User::query()
            ->where('role', 'user')
            ->whereNotNull('withdrawn_at');

        if (isset($filters['join_type']) && $filters['join_type']) {
            $query->byJoinType($filters['join_type']);
        }

        if (isset($filters['withdrawal_date_start']) || isset($filters['withdrawal_date_end'])) {
            $query->byWithdrawalDateRange($filters['withdrawal_date_start'] ?? null, $filters['withdrawal_date_end'] ?? null);
        }

        if (isset($filters['search_type']) && isset($filters['search_term'])) {
            $query->search($filters['search_type'], $filters['search_term']);
        }

        return $query->orderBy('withdrawn_at', 'desc')->paginate($perPage)->withQueryString();
    }

    public function restoreMember(int $id): User
    {
        $member = $this->getMember($id);
        $member->update(['withdrawn_at' => null]);

        return $member;
    }

    public function forceDeleteMember(int $id): User
    {
        $member = User::query()->where('role', 'user')->whereNotNull('withdrawn_at')->findOrFail($id);
        $member->delete();

        return $member;
    }

    public function forceDeleteMembers(array $ids): int
    {
        return User::query()->whereKey($ids)
            ->where('role', 'user')
            ->whereNotNull('withdrawn_at')
            ->delete();
    }

    public function exportMembersToCsv(array $filters = [])
    {
        $query = User::query()->where('role', 'user')->whereNull('withdrawn_at');
        $this->applyMemberListFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function checkDuplicateEmail(string $email, ?int $excludeId = null): bool
    {
        $query = User::query()->where('email', $email)->where('role', 'user');
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function checkDuplicatePhone(string $phone, ?int $excludeId = null): bool
    {
        $phone = User::normalizePhone($phone);
        $query = User::query()->where('phone_number', $phone)->where('role', 'user');
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * @param  Builder<User>  $query
     */
    private function applyExecutiveStatusFilter(Builder $query, string $status): void
    {
        if ($status === 'all' || $status === '') {
            return;
        }
        $activeExecutiveScope = function (Builder $executiveQuery): void {
            $today = now()->toDateString();
            $executiveQuery
                ->where('is_active', true)
                ->whereDate('term_start_date', '<=', $today)
                ->where(function (Builder $termQuery) use ($today) {
                    $termQuery->where('is_indefinite', true)
                        ->orWhereDate('term_end_date', '>=', $today);
                });
        };

        if ($status === 'executive') {
            $query->whereHas('memberExecutives', $activeExecutiveScope);

            return;
        }

        if ($status === 'non_executive') {
            $query->whereDoesntHave('memberExecutives', $activeExecutiveScope);
        }
    }
}
