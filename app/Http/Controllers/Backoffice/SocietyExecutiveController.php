<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocietyExecutiveRequest;
use App\Models\SocietyExecutive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SocietyExecutiveController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [20, 50, 100], true) ? $perPage : 20;

        $query = SocietyExecutive::query();

        if ($request->filled('group_no')) {
            $query->where('group_no', (int) $request->input('group_no'));
        }

        if ($request->filled('search_type') && $request->filled('keyword')) {
            $searchType = (string) $request->input('search_type');
            $keyword = trim((string) $request->input('keyword'));
            $columnMap = [
                'name' => 'name',
                'position' => 'position',
                'organization' => 'organization',
            ];

            if ($keyword !== '' && isset($columnMap[$searchType])) {
                $query->where($columnMap[$searchType], 'like', '%'.$keyword.'%');
            }
        }

        $executives = $query
            ->orderBy('group_no')
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('backoffice.society_executives.index', compact('executives', 'perPage'));
    }

    public function create()
    {
        return view('backoffice.society_executives.create');
    }

    public function store(SocietyExecutiveRequest $request)
    {
        $validated = $this->prepareValidatedData($request);

        SocietyExecutive::create($validated);

        return redirect()
            ->route('backoffice.society-executives.index')
            ->with('success', '임원 정보가 등록되었습니다.');
    }

    public function edit(SocietyExecutive $societyExecutive)
    {
        return view('backoffice.society_executives.edit', compact('societyExecutive'));
    }

    public function update(SocietyExecutiveRequest $request, SocietyExecutive $societyExecutive)
    {
        $validated = $this->prepareValidatedData($request, $societyExecutive);

        $societyExecutive->update($validated);

        return redirect()
            ->route('backoffice.society-executives.index')
            ->with('success', '임원 정보가 수정되었습니다.');
    }

    public function destroy(SocietyExecutive $societyExecutive)
    {
        $this->deletePhotoFile($societyExecutive->photo_path);
        $societyExecutive->delete();

        return redirect()
            ->route('backoffice.society-executives.index')
            ->with('success', '임원 정보가 삭제되었습니다.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'executive_ids' => ['required', 'array'],
            'executive_ids.*' => ['integer', 'exists:society_executives,id'],
        ]);

        $targets = SocietyExecutive::whereIn('id', $validated['executive_ids'])->get(['id', 'photo_path']);
        foreach ($targets as $target) {
            $this->deletePhotoFile($target->photo_path);
        }

        $deletedCount = SocietyExecutive::whereIn('id', $validated['executive_ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => $deletedCount.'명의 임원 정보가 삭제되었습니다.',
            'deleted_count' => $deletedCount,
        ]);
    }

    public function updateSortOrder(Request $request)
    {
        $validated = $request->validate([
            'updates' => ['required', 'array'],
            'updates.*.post_id' => ['required', 'integer', 'exists:society_executives,id'],
            'updates.*.sort_order' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['updates'] as $update) {
                SocietyExecutive::where('id', (int) $update['post_id'])
                    ->update([
                        'sort_order' => (int) $update['sort_order'],
                        'updated_at' => now(),
                    ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => '정렬 순서가 저장되었습니다.',
        ]);
    }

    private function prepareValidatedData(SocietyExecutiveRequest $request, ?SocietyExecutive $societyExecutive = null): array
    {
        $validated = $request->validated();
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 1);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        if ((int) $validated['group_no'] !== 1) {
            if ($societyExecutive instanceof SocietyExecutive) {
                $this->deletePhotoFile($societyExecutive->photo_path);
            }
            $validated['photo_path'] = null;

            return $validated;
        }

        if ($request->hasFile('photo')) {
            if ($societyExecutive instanceof SocietyExecutive) {
                $this->deletePhotoFile($societyExecutive->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('uploads/society-executives', 'public');
        } elseif ((bool) $request->input('remove_photo', false) === true) {
            if ($societyExecutive instanceof SocietyExecutive) {
                $this->deletePhotoFile($societyExecutive->photo_path);
            }
            $validated['photo_path'] = null;
        } elseif ($societyExecutive instanceof SocietyExecutive) {
            $validated['photo_path'] = $societyExecutive->photo_path;
        }

        return $validated;
    }

    private function deletePhotoFile(?string $photoPath): void
    {
        if (! $photoPath) {
            return;
        }

        if (Storage::disk('public')->exists($photoPath)) {
            Storage::disk('public')->delete($photoPath);
        }
    }
}
