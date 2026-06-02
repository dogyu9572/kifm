<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnualScheduleRequest;
use App\Models\AnnualSchedule;
use Illuminate\Http\Request;

class AnnualScheduleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [20, 50, 100], true) ? $perPage : 20;

        $query = AnnualSchedule::query();

        if ($request->filled('keyword')) {
            $keyword = (string) $request->input('keyword');
            $query->where('title', 'like', '%'.$keyword.'%');
        }
        if ($request->filled('start_date')) {
            $query->whereDate('end_date', '>=', (string) $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('start_date', '<=', (string) $request->input('end_date'));
        }
        if ($request->filled('is_visible')) {
            $query->where('is_visible', (bool) $request->boolean('is_visible'));
        }

        $schedules = $query
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('backoffice.annual_schedule.index', compact('schedules', 'perPage'));
    }

    public function create()
    {
        return view('backoffice.annual_schedule.create');
    }

    public function store(AnnualScheduleRequest $request)
    {
        $validated = $request->validated();

        if ((bool) ($validated['is_single_day'] ?? false)) {
            $validated['end_date'] = $validated['start_date'];
        }

        AnnualSchedule::create($validated);

        return redirect()
            ->route('backoffice.annual_schedule.index')
            ->with('success', '연간 일정이 등록되었습니다.');
    }

    public function edit(AnnualSchedule $annualSchedule)
    {
        return view('backoffice.annual_schedule.edit', compact('annualSchedule'));
    }

    public function update(AnnualScheduleRequest $request, AnnualSchedule $annualSchedule)
    {
        $validated = $request->validated();

        if ((bool) ($validated['is_single_day'] ?? false)) {
            $validated['end_date'] = $validated['start_date'];
        }

        $annualSchedule->update($validated);

        return redirect()
            ->route('backoffice.annual_schedule.index')
            ->with('success', '연간 일정이 수정되었습니다.');
    }

    public function destroy(AnnualSchedule $annualSchedule)
    {
        $annualSchedule->delete();

        return redirect()
            ->route('backoffice.annual_schedule.index')
            ->with('success', '연간 일정이 삭제되었습니다.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'schedule_ids' => ['required', 'array'],
            'schedule_ids.*' => ['integer', 'exists:annual_schedules,id'],
        ]);

        $deletedCount = AnnualSchedule::whereIn('id', $validated['schedule_ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => $deletedCount.'개의 일정이 삭제되었습니다.',
            'deleted_count' => $deletedCount,
        ]);
    }
}
