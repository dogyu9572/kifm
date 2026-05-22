<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\EduTrainingRequest;
use App\Models\EduTraining;
use App\Models\EduTrainingAttachment;
use App\Services\Backoffice\EduTrainingService;
use App\Support\BackofficeFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EduTrainingController extends Controller
{
    public function __construct(
        protected EduTrainingService $eduTrainingService
    ) {}

    public function index(Request $request)
    {
        $trainings = $this->eduTrainingService->paginateFiltered($request);

        return view('backoffice.edu-trainings.index', [
            'trainings' => $trainings,
            'perPage' => $trainings->perPage(),
            'seasonLabels' => EduTrainingService::seasonLabels(),
            'roundTypeLabels' => EduTrainingService::roundTypeLabels(),
            'methodLabels' => EduTrainingService::methodLabels(),
            'statusLabels' => EduTrainingService::statusLabels(),
            'yearOptions' => $this->yearOptions(),
        ]);
    }

    public function create(Request $request)
    {
        return view('backoffice.edu-trainings.create', [
            'eduTraining' => null,
            'cancelUrl' => $this->cancelUrl($request),
            'seasonLabels' => EduTrainingService::seasonLabels(),
            'roundTypeLabels' => EduTrainingService::roundTypeLabels(),
            'methodLabels' => EduTrainingService::methodLabels(),
            'statusLabels' => EduTrainingService::statusLabels(),
            'yearOptions' => $this->yearOptions(),
            'gradeLabels' => EduTrainingService::registrationGradeLabels(),
            'attachments' => collect(),
        ]);
    }

    public function store(EduTrainingRequest $request)
    {
        $training = $this->eduTrainingService->create($request->validated());
        $this->syncTextbookFile($request, $training);
        $this->syncAttachments($request, $training);

        return redirect()
            ->route('backoffice.edu-trainings.index')
            ->with('success', '연수교육이 등록되었습니다.');
    }

    public function edit(Request $request, EduTraining $eduTraining)
    {
        $eduTraining->load(['rounds', 'attachments']);

        return view('backoffice.edu-trainings.edit', [
            'eduTraining' => $eduTraining,
            'cancelUrl' => $this->cancelUrl($request),
            'seasonLabels' => EduTrainingService::seasonLabels(),
            'roundTypeLabels' => EduTrainingService::roundTypeLabels(),
            'methodLabels' => EduTrainingService::methodLabels(),
            'statusLabels' => EduTrainingService::statusLabels(),
            'yearOptions' => $this->yearOptions(),
            'gradeLabels' => EduTrainingService::registrationGradeLabels(),
            'attachments' => $eduTraining->attachments,
        ]);
    }

    public function update(EduTrainingRequest $request, EduTraining $eduTraining)
    {
        $this->eduTrainingService->update($eduTraining, $request->validated());
        $this->syncTextbookFile($request, $eduTraining);
        $this->syncAttachments($request, $eduTraining);

        return redirect()
            ->route('backoffice.edu-trainings.index')
            ->with('success', '연수교육이 수정되었습니다.');
    }

    public function destroy(EduTraining $eduTraining)
    {
        if ($eduTraining->textbook_file_path && Storage::disk('public')->exists($eduTraining->textbook_file_path)) {
            Storage::disk('public')->delete($eduTraining->textbook_file_path);
        }
        $eduTraining->delete();

        return redirect()
            ->route('backoffice.edu-trainings.index')
            ->with('success', '연수교육이 삭제되었습니다.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'training_ids' => ['required', 'array'],
            'training_ids.*' => ['integer', 'exists:edu_trainings,id'],
        ]);

        $deleted = $this->eduTrainingService->deleteMany($validated['training_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted.'건의 연수교육이 삭제되었습니다.',
            'deleted_count' => $deleted,
        ]);
    }

    /**
     * @return list<int>
     */
    protected function yearOptions(): array
    {
        $baseYear = (int) now()->format('Y');

        return [$baseYear, $baseYear - 1, $baseYear - 2, $baseYear - 3];
    }

    protected function cancelUrl(Request $request): string
    {
        $fallback = route('backoffice.edu-trainings.index');
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
            $query = isset($parts['query']) ? '?'.$parts['query'] : '';

            return $parts['path'].$query;
        }

        return $fallback;
    }

    protected function syncTextbookFile(Request $request, EduTraining $training): void
    {
        if ($request->boolean('delete_textbook_file') && $training->textbook_file_path) {
            if (Storage::disk('public')->exists($training->textbook_file_path)) {
                Storage::disk('public')->delete($training->textbook_file_path);
            }
            $training->textbook_file_path = null;
            $training->save();
        }

        if (! $request->hasFile('textbook_file')) {
            return;
        }

        $path = BackofficeFile::storeWithOriginalName($request->file('textbook_file'), 'backoffice/edu-trainings/textbooks', 'public');
        $this->eduTrainingService->replaceTextbookFile($training->fresh(), $path);
    }

    protected function syncAttachments(EduTrainingRequest $request, EduTraining $training): void
    {
        $validated = $request->validated();
        $deleteIds = $validated['delete_attachment_ids'] ?? [];
        if (is_array($deleteIds) && $deleteIds !== []) {
            EduTrainingAttachment::query()
                ->where('edu_training_id', $training->id)
                ->whereIn('id', $deleteIds)
                ->get()
                ->each
                ->delete();
        }

        $files = $request->file('attachment_files', []);
        if (! is_array($files)) {
            $files = array_filter([$files]);
        }

        $training = $training->fresh();
        if (! $training) {
            return;
        }

        $maxSort = (int) $training->attachments()->max('sort_order');
        $n = 0;
        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }
            $path = BackofficeFile::storeWithOriginalName($file, 'backoffice/edu-trainings/attachments', 'public');
            $training->attachments()->create([
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'sort_order' => $maxSort + (++$n),
            ]);
        }
    }
}
