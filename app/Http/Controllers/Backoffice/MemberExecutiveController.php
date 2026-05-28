<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemberExecutiveRequest;
use App\Models\MemberExecutive;
use App\Models\User;
use App\Services\Backoffice\MemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemberExecutiveController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [20, 50, 100], true) ? $perPage : 20;

        $query = MemberExecutive::query()->with('member');
        $roles = $request->input('roles', []);
        if (is_array($roles) && $roles !== [] && ! in_array('all', $roles, true)) {
            $query->whereIn('executive_role', $roles);
        }

        $termStatus = (string) $request->get('term_status', 'all');
        $today = now()->toDateString();
        if ($termStatus === 'active') {
            $query->whereDate('term_start_date', '<=', $today)
                ->where(function ($q) use ($today) {
                    $q->where('is_indefinite', true)->orWhereDate('term_end_date', '>=', $today);
                });
        } elseif ($termStatus === 'expired') {
            $query->where('is_indefinite', false)->whereDate('term_end_date', '<', $today);
        } elseif ($termStatus === 'upcoming') {
            $query->whereDate('term_start_date', '>', $today);
        }

        $searchField = (string) $request->get('search_field', 'name');
        $searchKeyword = trim((string) $request->get('search_keyword', ''));
        if ($searchKeyword !== '') {
            $query->whereHas('member', function ($memberQuery) use ($searchField, $searchKeyword) {
                $column = match ($searchField) {
                    'email' => 'email',
                    'phone' => 'phone_number',
                    default => 'name',
                };
                $memberQuery->where($column, 'like', '%' . $searchKeyword . '%');
            });
        }

        $executives = $query
            ->orderByDesc('term_start_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('backoffice.member_executives.index', [
            'executives' => $executives,
            'perPage' => $perPage,
            'roleLabels' => MemberExecutive::roleLabels(),
            'memberLevelLabels' => MemberService::memberLevelLabels(),
        ]);
    }

    public function create(Request $request)
    {
        $executive = new MemberExecutive([
            'is_active' => true,
        ]);
        $member = $this->memberFromRequest($request);
        if ($member) {
            $executive->member_id = $member->id;
            $executive->setRelation('member', $member);
        }

        return view('backoffice.member_executives.create', [
            'executive' => $executive,
            'roleLabels' => MemberExecutive::roleLabels(),
            'returnUrl' => $this->safeBackofficeReturnUrl($request, route('backoffice.member-executives.index')),
        ]);
    }

    public function store(MemberExecutiveRequest $request): RedirectResponse
    {
        $payload = $this->buildPayload($request->validated());
        MemberExecutive::query()->create($payload);

        return redirect()
            ->to($this->safeBackofficeReturnUrl($request, route('backoffice.member-executives.index')))
            ->with('success', '임원 정보가 등록되었습니다.');
    }

    public function edit(Request $request, MemberExecutive $memberExecutive)
    {
        $memberExecutive->load('member');

        return view('backoffice.member_executives.edit', [
            'executive' => $memberExecutive,
            'roleLabels' => MemberExecutive::roleLabels(),
            'returnUrl' => $this->safeBackofficeReturnUrl($request, route('backoffice.member-executives.index')),
        ]);
    }

    public function searchMembers(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $searchField = (string) $request->get('search_field', 'all');
        $perPage = 10;

        $query = User::query()
            ->select(['id', 'name', 'login_id', 'email', 'phone_number'])
            ->where('role', 'user')
            ->whereNull('withdrawn_at');

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
                }
            });
        } elseif ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('login_id', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('phone_number', 'like', '%' . $keyword . '%');
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

    public function update(MemberExecutiveRequest $request, MemberExecutive $memberExecutive): RedirectResponse
    {
        $payload = $this->buildPayload($request->validated());
        $memberExecutive->update($payload);

        return redirect()
            ->to($this->safeBackofficeReturnUrl($request, route('backoffice.member-executives.index')))
            ->with('success', '임원 정보가 수정되었습니다.');
    }

    public function destroy(Request $request, MemberExecutive $memberExecutive): RedirectResponse
    {
        $memberExecutive->delete();

        return redirect($this->safeBackofficeReturnUrl($request, route('backoffice.member-executives.index')))
            ->with('success', '임원 정보가 삭제되었습니다.');
    }

    private function buildPayload(array $validated): array
    {
        $validated['is_indefinite'] = (bool) ($validated['is_indefinite'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        if ($validated['is_indefinite']) {
            $validated['term_end_date'] = null;
        }

        return $validated;
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

}
