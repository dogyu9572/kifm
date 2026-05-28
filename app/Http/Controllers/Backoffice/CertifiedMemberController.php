<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertifiedMemberRequest;
use App\Models\CertifiedMember;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CertifiedMemberController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->get('status', 'all'),
            'validity_start' => $request->get('validity_start'),
            'validity_end' => $request->get('validity_end'),
            'remaining_period' => $request->get('remaining_period', 'all'),
            'search_field' => $request->get('search_field', 'name'),
            'keyword' => trim((string) $request->get('keyword', '')),
        ];

        $query = CertifiedMember::query()->with('member');
        $today = Carbon::today();

        if ($filters['status'] === 'active') {
            $query->whereDate('validity_end_date', '>=', $today);
        } elseif ($filters['status'] === 'expired') {
            $query->whereDate('validity_end_date', '<', $today);
        }

        if (!empty($filters['validity_start'])) {
            $query->whereDate('validity_start_date', '>=', $filters['validity_start']);
        }

        if (!empty($filters['validity_end'])) {
            $query->whereDate('validity_end_date', '<=', $filters['validity_end']);
        }

        if ($filters['remaining_period'] !== 'all') {
            $days = $this->remainingPeriodDays($filters['remaining_period']);
            if ($days !== null) {
                $query->whereDate('validity_end_date', '>=', $today)
                    ->whereDate('validity_end_date', '<=', $today->copy()->addDays($days));
            }
        }

        if ($filters['keyword'] !== '') {
            $query->whereHas('member', function ($memberQuery) use ($filters) {
                $field = $filters['search_field'];
                $keyword = $filters['keyword'];

                if ($field === 'id') {
                    $memberQuery->where('login_id', 'like', '%' . $keyword . '%');
                } elseif ($field === 'license') {
                    $memberQuery->where('license_number', 'like', '%' . $keyword . '%');
                } else {
                    $memberQuery->where('name', 'like', '%' . $keyword . '%');
                }
            });
        }

        $certifiedMembers = $query->latest()->paginate(20)->withQueryString();

        return view('backoffice.certified_members.index', [
            'certifiedMembers' => $certifiedMembers,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): View
    {
        $certifiedMember = new CertifiedMember();
        $member = $this->memberFromRequest($request);
        if ($member) {
            $certifiedMember->member_id = $member->id;
            $certifiedMember->setRelation('member', $member);
        }

        return view('backoffice.certified_members.create', array_merge(
            $this->buildPayload($request),
            ['certifiedMember' => $certifiedMember]
        ));
    }

    public function store(CertifiedMemberRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $certifiedMember = CertifiedMember::query()->updateOrCreate(
            ['member_id' => $validated['member_id']],
            [
                'validity_start_date' => $validated['validity_start_date'],
                'validity_end_date' => $validated['validity_end_date'],
                'acquired_date' => $validated['acquired_date'],
                'acquired_validity_start' => $validated['acquired_validity_start'],
                'acquired_validity_end' => $validated['acquired_validity_end'],
                'winter_course_completed' => (bool) ($validated['winter_course_completed'] ?? false),
                'exam_passed' => (bool) ($validated['exam_passed'] ?? false),
            ]
        );

        User::query()->whereKey($certifiedMember->member_id)->update(['certified_instructor' => true]);

        return redirect($this->safeBackofficeReturnUrl($request, route('backoffice.certified-members.edit', $certifiedMember)))
            ->with('success', '인정의 정보가 저장되었습니다.');
    }

    public function edit(Request $request, CertifiedMember $certifiedMember): View
    {
        return view('backoffice.certified_members.edit', array_merge(
            $this->buildPayload($request),
            ['certifiedMember' => $certifiedMember->load(['member', 'renewals'])]
        ));
    }

    public function update(CertifiedMemberRequest $request, CertifiedMember $certifiedMember): RedirectResponse
    {
        $validated = $request->validated();

        $certifiedMember->update([
            'member_id' => $validated['member_id'],
            'validity_start_date' => $validated['validity_start_date'],
            'validity_end_date' => $validated['validity_end_date'],
            'acquired_date' => $validated['acquired_date'],
            'acquired_validity_start' => $validated['acquired_validity_start'],
            'acquired_validity_end' => $validated['acquired_validity_end'],
            'winter_course_completed' => (bool) ($validated['winter_course_completed'] ?? false),
            'exam_passed' => (bool) ($validated['exam_passed'] ?? false),
        ]);

        User::query()->whereKey($certifiedMember->member_id)->update(['certified_instructor' => true]);

        return redirect($this->safeBackofficeReturnUrl($request, route('backoffice.certified-members.edit', $certifiedMember)))
            ->with('success', '인정의 정보가 수정되었습니다.');
    }

    public function destroy(Request $request, CertifiedMember $certifiedMember): RedirectResponse
    {
        $memberId = $certifiedMember->member_id;
        $certifiedMember->delete();

        $hasRemaining = CertifiedMember::query()->where('member_id', $memberId)->exists();
        User::query()->whereKey($memberId)->update(['certified_instructor' => $hasRemaining]);

        return redirect($this->safeBackofficeReturnUrl($request, route('backoffice.certified-members.index')))
            ->with('success', '인정의 정보가 삭제되었습니다.');
    }

    public function searchMembers(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $searchField = (string) $request->get('search_field', 'all');
        $perPage = 10;

        $query = User::query()
            ->select(['id', 'name', 'login_id', 'email', 'phone_number', 'license_number'])
            ->notWithdrawn();

        if ($keyword !== '' && $searchField !== 'all') {
            $query->where(function ($q) use ($keyword, $searchField) {
                if ($searchField === 'name') {
                    $q->where('name', 'like', '%' . $keyword . '%');
                } elseif ($searchField === 'id') {
                    $q->where('login_id', 'like', '%' . $keyword . '%');
                } elseif ($searchField === 'email') {
                    $q->where('email', 'like', '%' . $keyword . '%');
                } elseif ($searchField === 'phone') {
                    $q->where('phone_number', 'like', '%' . $keyword . '%');
                } elseif ($searchField === 'license') {
                    $q->where('license_number', 'like', '%' . $keyword . '%');
                }
            });
        } elseif ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('login_id', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('phone_number', 'like', '%' . $keyword . '%')
                    ->orWhere('license_number', 'like', '%' . $keyword . '%');
            });
        }

        $members = $query->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        $items = $members->getCollection()
            ->map(fn (User $member) => [
                'id' => $member->id,
                'name' => $member->name,
                'login_id' => $member->login_id,
                'email' => $member->email,
                'phone_number' => $member->phone_number,
                'license_number' => $member->license_number,
            ]);

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'total' => $members->total(),
                'per_page' => $members->perPage(),
            ],
        ]);
    }

    private function buildPayload(Request $request): array
    {
        return [
            'returnUrl' => $this->safeBackofficeReturnUrl($request, route('backoffice.certified-members.index')),
        ];
    }

    private function memberFromRequest(Request $request): ?User
    {
        $memberId = $request->integer('member_id');
        if ($memberId <= 0) {
            return null;
        }

        return User::query()
            ->where('role', 'user')
            ->whereNull('withdrawn_at')
            ->find($memberId);
    }

    private function safeBackofficeReturnUrl(Request $request, string $fallback): string
    {
        $returnUrl = (string) $request->input('return_url', '');
        if ($returnUrl !== '' && str_starts_with($returnUrl, '/backoffice/') && ! str_starts_with($returnUrl, '//')) {
            return $returnUrl;
        }

        return $fallback;
    }

    private function remainingPeriodDays(string $period): ?int
    {
        return match ($period) {
            '1y' => 365,
            '6m' => 183,
            '3m' => 92,
            '1m' => 31,
            '2w' => 14,
            '1w' => 7,
            default => null,
        };
    }
}
