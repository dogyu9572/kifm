<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\CommunityCommitteeRequest;
use App\Models\CommunityCommittee;
use App\Models\CommunityCommitteeApplication;
use App\Models\User;
use App\Services\Backoffice\CommunityCommitteeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CommunityCommitteeController extends Controller
{
    public function __construct(
        protected CommunityCommitteeService $communityCommitteeService
    ) {}

    public function index(Request $request)
    {
        $committees = $this->communityCommitteeService->paginateFiltered($request);

        return view('backoffice.community-committees.index', [
            'committees' => $committees,
            'perPage' => $committees->perPage(),
            'typeLabels' => CommunityCommitteeService::committeeTypeLabels(),
            'visibilityLabels' => CommunityCommitteeService::visibilityLabels(),
        ]);
    }

    public function create(Request $request)
    {
        return view('backoffice.community-committees.create', [
            'committee' => new CommunityCommittee([
                'committee_type' => 'general',
                'pending_count' => 0,
                'member_count' => 0,
                'visibility_yn' => 'Y',
                'is_mandatory' => 'N',
            ]),
            'typeLabels' => CommunityCommitteeService::committeeTypeLabels(),
            'roleLabels' => CommunityCommitteeService::roleLabels(),
            'cancelUrl' => $this->cancelUrl($request),
        ]);
    }

    public function store(CommunityCommitteeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $data) {
            $committee = new CommunityCommittee();
            $committee->fill($this->payload($data));

            if ($request->hasFile('thumbnail')) {
                $committee->thumbnail_path = $request->file('thumbnail')->store('backoffice/community-committees', 'public');
            }
            if ($request->hasFile('banner')) {
                $committee->banner_path = $request->file('banner')->store('backoffice/community-committees/banners', 'public');
            }
            $committee->save();
            $this->syncCommitteeMembers($committee, $data);
            $committee->member_count = $committee->committeeMembers()->count();
            $committee->save();
        });

        return redirect()->route('backoffice.community-committees.index')
            ->with('success', '위원회가 등록되었습니다.');
    }

    public function edit(Request $request, CommunityCommittee $communityCommittee)
    {
        return view('backoffice.community-committees.edit', [
            'committee' => $communityCommittee->load('committeeMembers.user'),
            'typeLabels' => CommunityCommitteeService::committeeTypeLabels(),
            'roleLabels' => CommunityCommitteeService::roleLabels(),
            'cancelUrl' => $this->cancelUrl($request),
        ]);
    }

    public function update(CommunityCommitteeRequest $request, CommunityCommittee $communityCommittee): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $data, $communityCommittee) {
            $communityCommittee->fill($this->payload($data));

            if (($data['delete_thumbnail'] ?? false) && $communityCommittee->thumbnail_path) {
                Storage::disk('public')->delete($communityCommittee->thumbnail_path);
                $communityCommittee->thumbnail_path = null;
            }

            if (($data['delete_banner'] ?? false) && $communityCommittee->banner_path) {
                Storage::disk('public')->delete($communityCommittee->banner_path);
                $communityCommittee->banner_path = null;
            }

            if ($request->hasFile('thumbnail')) {
                if ($communityCommittee->thumbnail_path) {
                    Storage::disk('public')->delete($communityCommittee->thumbnail_path);
                }
                $communityCommittee->thumbnail_path = $request->file('thumbnail')->store('backoffice/community-committees', 'public');
            }
            if ($request->hasFile('banner')) {
                if ($communityCommittee->banner_path) {
                    Storage::disk('public')->delete($communityCommittee->banner_path);
                }
                $communityCommittee->banner_path = $request->file('banner')->store('backoffice/community-committees/banners', 'public');
            }

            $communityCommittee->save();
            $this->syncCommitteeMembers($communityCommittee, $data);
            $communityCommittee->member_count = $communityCommittee->committeeMembers()->count();
            $communityCommittee->save();
        });

        return redirect()->route('backoffice.community-committees.index')
            ->with('success', '위원회가 수정되었습니다.');
    }

    public function destroy(CommunityCommittee $communityCommittee): RedirectResponse
    {
        if ($communityCommittee->thumbnail_path) {
            Storage::disk('public')->delete($communityCommittee->thumbnail_path);
        }
        if ($communityCommittee->banner_path) {
            Storage::disk('public')->delete($communityCommittee->banner_path);
        }
        CommunityCommittee::query()->whereKey($communityCommittee->id)->delete();

        return redirect()->route('backoffice.community-committees.index')
            ->with('success', '위원회가 삭제되었습니다.');
    }

    public function applicants(Request $request)
    {
        $committeeId = (int) $request->query('committee_id', 0);
        $status = strtoupper(trim((string) $request->query('status', '')));
        $keyword = trim((string) $request->query('keyword', ''));
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;

        $selected = $committeeId > 0 ? CommunityCommittee::query()->find($committeeId) : null;
        $committees = CommunityCommittee::query()
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        $applications = CommunityCommitteeApplication::query()
            ->with(['committee:id,name'])
            ->when($committeeId > 0, fn ($query) => $query->where('community_committee_id', $committeeId))
            ->when(in_array($status, ['PENDING', 'APPROVED', 'REJECTED'], true), fn ($query) => $query->where('status', $status))
            ->when($keyword !== '', function ($query) use ($keyword) {
                $like = '%' . $keyword . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('applicant_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                });
            })
            ->orderByDesc('applied_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('backoffice.community-committees.applicants', [
            'committees' => $committees,
            'selectedCommittee' => $selected,
            'applications' => $applications,
            'statusLabels' => [
                'PENDING' => '대기',
                'APPROVED' => '승인',
                'REJECTED' => '반려',
            ],
            'perPage' => $perPage,
        ]);
    }

    public function approveApplicant(Request $request, CommunityCommitteeApplication $application): RedirectResponse
    {
        if ($application->status !== 'PENDING') {
            return redirect($this->applicantsReturnUrl($request))->with('error', '대기 상태 신청만 승인할 수 있습니다.');
        }

        DB::transaction(function () use ($application) {
            $committee = CommunityCommittee::query()->find($application->community_committee_id);
            if (! $committee) {
                return;
            }

            $memberLimit = $committee->member_limit;
            if ($memberLimit !== null) {
                $currentCount = $committee->committeeMembers()->count();
                if ($currentCount >= $memberLimit) {
                    throw new \RuntimeException('정원이 가득 찼습니다. 승인할 수 없습니다.');
                }
            }

            if ($application->user_id) {
                $committee->committeeMembers()->firstOrCreate(
                    ['user_id' => $application->user_id],
                    ['role' => 'member']
                );
            }

            $application->status = 'APPROVED';
            $application->reject_reason = null;
            $application->processed_at = now();
            $application->processed_by = Auth::id();
            $application->save();

            $committee->member_count = $committee->committeeMembers()->count();
            $committee->pending_count = CommunityCommitteeApplication::query()
                ->where('community_committee_id', $committee->id)
                ->where('status', 'PENDING')
                ->count();
            $committee->save();
        });

        return redirect($this->applicantsReturnUrl($request))->with('success', '신청을 승인했습니다.');
    }

    public function rejectApplicant(Request $request, CommunityCommitteeApplication $application): RedirectResponse
    {
        $reason = trim((string) $request->input('reject_reason', ''));
        if ($reason === '') {
            return redirect($this->applicantsReturnUrl($request))->with('error', '반려 사유를 입력해주세요.');
        }

        DB::transaction(function () use ($application, $reason) {
            $committee = CommunityCommittee::query()->find($application->community_committee_id);

            $application->status = 'REJECTED';
            $application->reject_reason = $reason;
            $application->processed_at = now();
            $application->processed_by = Auth::id();
            $application->save();

            if ($committee) {
                $committee->pending_count = CommunityCommitteeApplication::query()
                    ->where('community_committee_id', $committee->id)
                    ->where('status', 'PENDING')
                    ->count();
                $committee->save();
            }
        });

        return redirect($this->applicantsReturnUrl($request))->with('success', '신청을 반려 처리했습니다.');
    }

    public function cancelApproval(Request $request, CommunityCommitteeApplication $application): RedirectResponse
    {
        if ($application->status !== 'APPROVED') {
            return redirect($this->applicantsReturnUrl($request))->with('error', '승인 상태 신청만 취소할 수 있습니다.');
        }

        $reason = trim((string) $request->input('reject_reason', ''));
        if ($reason === '') {
            return redirect($this->applicantsReturnUrl($request))->with('error', '승인 취소 사유를 입력해주세요.');
        }

        DB::transaction(function () use ($application, $reason) {
            $committee = CommunityCommittee::query()->find($application->community_committee_id);
            if (! $committee) {
                return;
            }

            if ($application->user_id) {
                $committee->committeeMembers()->where('user_id', $application->user_id)->delete();
            }

            $application->status = 'REJECTED';
            $application->reject_reason = $reason;
            $application->processed_at = now();
            $application->processed_by = Auth::id();
            $application->save();

            $committee->member_count = $committee->committeeMembers()->count();
            $committee->pending_count = CommunityCommitteeApplication::query()
                ->where('community_committee_id', $committee->id)
                ->where('status', 'PENDING')
                ->count();
            $committee->save();
        });

        return redirect($this->applicantsReturnUrl($request))->with('success', '승인을 취소했습니다.');
    }

    public function exportApplicants(Request $request): Response
    {
        $committeeId = (int) $request->query('committee_id', 0);
        $status = strtoupper(trim((string) $request->query('status', '')));
        $keyword = trim((string) $request->query('keyword', ''));

        $rows = CommunityCommitteeApplication::query()
            ->with(['committee:id,name'])
            ->when($committeeId > 0, fn ($query) => $query->where('community_committee_id', $committeeId))
            ->when(in_array($status, ['PENDING', 'APPROVED', 'REJECTED'], true), fn ($query) => $query->where('status', $status))
            ->when($keyword !== '', function ($query) use ($keyword) {
                $like = '%' . $keyword . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('applicant_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                });
            })
            ->orderByDesc('applied_at')
            ->orderByDesc('id')
            ->get();

        $lines = [];
        $lines[] = "\xEF\xBB\xBFNo,위원회명,신청자,이메일,연락처,신청일,상태,처리일시,반려사유";
        foreach ($rows as $index => $row) {
            $statusLabel = match ($row->status) {
                'APPROVED' => '승인',
                'REJECTED' => '반려',
                default => '대기',
            };
            $line = [
                (string) ($index + 1),
                $row->committee->name ?? '-',
                $row->applicant_name ?? '-',
                $row->email ?? '-',
                $row->phone ?? '-',
                optional($row->applied_at)->format('Y-m-d') ?? '-',
                $statusLabel,
                optional($row->processed_at)->format('Y-m-d H:i') ?? '-',
                $row->reject_reason ?? '-',
            ];
            $lines[] = implode(',', array_map(static fn ($value) => '"' . str_replace('"', '""', (string) $value) . '"', $line));
        }

        $filename = 'community-committee-applicants-' . Carbon::now()->format('Ymd_His') . '.csv';

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function searchMembers(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $field = (string) $request->get('search_field', 'all');

        $members = User::query()
            ->select(['id', 'name', 'email', 'workplace_name', 'position', 'login_id', 'phone_number'])
            ->notWithdrawn()
            ->when($keyword !== '', function ($query) use ($keyword, $field) {
                $query->where(function ($q) use ($keyword, $field) {
                    $like = '%' . $keyword . '%';
                    if ($field === 'name') {
                        $q->where('name', 'like', $like);
                        return;
                    }
                    if ($field === 'email') {
                        $q->where('email', 'like', $like);
                        return;
                    }
                    if ($field === 'id') {
                        $q->where('login_id', 'like', $like);
                        return;
                    }
                    if ($field === 'phone') {
                        $q->where('phone_number', 'like', $like);
                        return;
                    }
                    $q->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('login_id', 'like', $like)
                        ->orWhere('phone_number', 'like', $like)
                        ->orWhere('workplace_name', 'like', $like);
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'data' => $members->getCollection()->map(fn (User $member) => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'organization' => $member->workplace_name,
                'position' => $member->position,
                'login_id' => $member->login_id,
                'phone_number' => $member->phone_number,
            ]),
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'total' => $members->total(),
                'per_page' => $members->perPage(),
            ],
        ]);
    }

    /** @param array<string,mixed> $data */
    protected function payload(array $data): array
    {
        return [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'committee_type' => $data['committee_type'],
            'pending_count' => (int) ($data['pending_count'] ?? 0),
            'member_count' => (int) ($data['member_count'] ?? 0),
            'member_limit' => ! empty($data['no_member_limit']) ? null : ($data['member_limit'] ?? null),
            'visibility_yn' => $data['visibility_yn'],
            'is_mandatory' => $data['is_mandatory'] ?? 'N',
            'regulation' => $data['regulation'] ?? null,
            'protocol' => $data['protocol'] ?? null,
        ];
    }

    /** @param array<string,mixed> $data */
    protected function syncCommitteeMembers(CommunityCommittee $committee, array $data): void
    {
        $committee->committeeMembers()->delete();

        $members = $data['committee_members'] ?? [];
        foreach ($members as $member) {
            $userId = (int) ($member['user_id'] ?? 0);
            if ($userId < 1) {
                continue;
            }
            $committee->committeeMembers()->create([
                'user_id' => $userId,
                'role' => $member['role'] ?? 'member',
            ]);
        }
    }

    protected function cancelUrl(Request $request): string
    {
        $fallback = route('backoffice.community-committees.index');
        $raw = $request->query('return_url');
        if (! is_string($raw) || $raw === '') {
            return $fallback;
        }
        $decoded = urldecode($raw);
        if (str_starts_with($decoded, '/backoffice/') && ! str_starts_with($decoded, '//')) {
            return $decoded;
        }

        return $fallback;
    }

    protected function applicantsReturnUrl(Request $request): string
    {
        $query = $request->input('return_query', $request->query('return_query', ''));
        $base = route('backoffice.community-committee-applicants.index');
        if (! is_string($query) || $query === '') {
            return $base;
        }

        return $base . '?' . ltrim($query, '?');
    }
}

