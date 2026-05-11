<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\EduCourseRequest;
use App\Models\EduCourse;
use App\Models\EduTraining;
use App\Models\User;
use App\Services\Backoffice\EduCourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EduCourseController extends Controller
{
    public function __construct(
        protected EduCourseService $eduCourseService
    ) {}

    public function index(Request $request)
    {
        $courses = $this->eduCourseService->paginateFiltered($request);

        return view('backoffice.edu-courses.index', [
            'courses' => $courses,
            'perPage' => $courses->perPage(),
            'courseTypeLabels' => EduCourseService::courseTypeLabels(),
            'searchFieldLabels' => EduCourseService::searchFieldLabels(),
            'yearOptions' => $this->yearOptions(),
        ]);
    }

    public function create(Request $request)
    {
        return view('backoffice.edu-courses.create', [
            'course' => new EduCourse([
                'course_type' => null,
                'open_year' => now()->year,
                'annual_fee_target' => 'all',
                'free_yn' => 'N',
                'period_type' => 'days',
                'duration_days' => 30,
                'exam_yn' => 'Y',
                'expose_yn' => 'Y',
                'use_yn' => 'Y',
            ]),
            'gradeLabels' => EduCourseService::gradeLabels(),
            'courseTypeLabels' => EduCourseService::courseTypeLabels(),
            'yearOptions' => $this->yearOptions(),
            'cancelUrl' => $this->cancelUrl($request),
            'linkedTrainings' => EduTraining::query()->orderByDesc('year')->orderByDesc('id')->get(['id', 'title']),
            'linkedEvents' => [],
        ]);
    }

    public function store(EduCourseRequest $request): RedirectResponse
    {
        $course = $this->eduCourseService->create($request->validated());
        $this->syncFiles($request, $course);

        return redirect()->route('backoffice.edu-courses.index')
            ->with('success', '강좌가 등록되었습니다.');
    }

    public function edit(Request $request, EduCourse $eduCourse)
    {
        $eduCourse->load(['gradePrices', 'examQuestions', 'linkSetting', 'professorMember']);

        return view('backoffice.edu-courses.edit', [
            'course' => $eduCourse,
            'gradeLabels' => EduCourseService::gradeLabels(),
            'courseTypeLabels' => EduCourseService::courseTypeLabels(),
            'yearOptions' => $this->yearOptions(),
            'cancelUrl' => $this->cancelUrl($request),
            'linkedTrainings' => EduTraining::query()->orderByDesc('year')->orderByDesc('id')->get(['id', 'title']),
            'linkedEvents' => [],
        ]);
    }

    public function update(EduCourseRequest $request, EduCourse $eduCourse): RedirectResponse
    {
        $this->eduCourseService->update($eduCourse, $request->validated());
        $freshCourse = EduCourse::query()->findOrFail($eduCourse->id);
        $this->syncFiles($request, $freshCourse);

        return redirect()->route('backoffice.edu-courses.index')
            ->with('success', '강좌가 수정되었습니다.');
    }

    public function destroy(EduCourse $eduCourse): RedirectResponse
    {
        $this->deleteFiles($eduCourse);
        EduCourse::query()->whereKey($eduCourse->id)->delete();

        return redirect()->route('backoffice.edu-courses.index')
            ->with('success', '강좌가 삭제되었습니다.');
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_ids' => ['required', 'array'],
            'course_ids.*' => ['integer', 'exists:edu_courses,id'],
        ]);

        $courses = EduCourse::query()->findMany($validated['course_ids']);
        foreach ($courses as $course) {
            $this->deleteFiles($course);
        }

        $deleted = $this->eduCourseService->deleteMany($validated['course_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted . '건의 강좌가 삭제되었습니다.',
            'deleted_count' => $deleted,
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
                    $q->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('login_id', 'like', $like)
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

    protected function syncFiles(Request $request, EduCourse $course): void
    {
        if ($request->boolean('delete_thumbnail') && $course->thumbnail_path) {
            Storage::disk('public')->delete($course->thumbnail_path);
            $course->thumbnail_path = null;
        }
        if ($request->boolean('delete_lecture_file') && $course->lecture_file_path) {
            Storage::disk('public')->delete($course->lecture_file_path);
            $course->lecture_file_path = null;
        }
        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail_path) {
                Storage::disk('public')->delete($course->thumbnail_path);
            }
            $course->thumbnail_path = $request->file('thumbnail')->store('backoffice/edu-courses/thumbnails', 'public');
        }
        if ($request->hasFile('lecture_file')) {
            if ($course->lecture_file_path) {
                Storage::disk('public')->delete($course->lecture_file_path);
            }
            $course->lecture_file_path = $request->file('lecture_file')->store('backoffice/edu-courses/lectures', 'public');
        }
        $course->save();
    }

    protected function deleteFiles(EduCourse $course): void
    {
        if ($course->thumbnail_path) {
            Storage::disk('public')->delete($course->thumbnail_path);
        }
        if ($course->lecture_file_path) {
            Storage::disk('public')->delete($course->lecture_file_path);
        }
    }

    /** @return list<int> */
    protected function yearOptions(): array
    {
        $base = (int) now()->format('Y');
        $years = [];
        for ($y = $base; $y >= 2010; $y--) {
            $years[] = $y;
        }

        return $years;
    }

    protected function cancelUrl(Request $request): string
    {
        $fallback = route('backoffice.edu-courses.index');
        $raw = $request->query('return_url');
        if (! is_string($raw) || $raw === '') {
            return $fallback;
        }

        $decoded = urldecode($raw);
        if (str_starts_with($decoded, '/backoffice/') && ! str_starts_with($decoded, '//')) {
            return $decoded;
        }

        $parts = parse_url($decoded);
        if (! empty($parts['path']) && str_starts_with($parts['path'], '/backoffice/')) {
            $query = isset($parts['query']) ? '?' . $parts['query'] : '';

            return $parts['path'] . $query;
        }

        return $fallback;
    }
}

