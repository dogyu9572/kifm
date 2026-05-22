<?php

namespace App\Services\Backoffice;

use App\Models\AcademicEventSession;
use App\Models\AcademicEventSessionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AcademicEventSessionItemService
{
    public function replaceItems(AcademicEventSession $session, array $items): void
    {
        $this->validateItems($session, $items);

        DB::transaction(function () use ($session, $items) {
            $session->items()->delete();

            foreach ($items as $index => $item) {
                $rowType = $item['row_type'] ?? 'abstract';
                AcademicEventSessionItem::query()->create([
                    'academic_event_session_id' => $session->id,
                    'academic_event_abstract_id' => $rowType === 'abstract' && ! empty($item['academic_event_abstract_id'])
                        ? (int) $item['academic_event_abstract_id']
                        : null,
                    'row_type' => $rowType,
                    'start_time' => $item['start_time'],
                    'end_time' => $item['end_time'],
                    'title' => $rowType === 'break' ? (($item['title'] ?? '') ?: 'Coffee Break') : ($item['title'] ?? null),
                    'presenter' => $rowType === 'break' ? null : ($item['presenter'] ?? null),
                    'sort_order' => $index + 1,
                ]);
            }
        });
    }

    private function validateItems(AcademicEventSession $session, array $items): void
    {
        $errors = [];
        $sessionStart = Carbon::parse((string) $session->start_time);
        $sessionEnd = Carbon::parse((string) $session->end_time);

        foreach ($items as $index => $item) {
            $row = $index + 1;
            $start = Carbon::parse((string) ($item['start_time'] ?? ''));
            $end = Carbon::parse((string) ($item['end_time'] ?? ''));
            $rowType = $item['row_type'] ?? 'abstract';

            if ($start->greaterThanOrEqualTo($end)) {
                $errors["items.{$index}.end_time"] = "{$row}번째 항목의 종료 시간은 시작 시간보다 늦어야 합니다.";
            }
            if ($start->lt($sessionStart) || $end->gt($sessionEnd)) {
                $errors["items.{$index}.start_time"] = "{$row}번째 항목 시간이 세션 시간 범위를 벗어났습니다.";
            }
            if ($rowType === 'abstract') {
                $abstractId = (int) ($item['academic_event_abstract_id'] ?? 0);
                if ($abstractId > 0) {
                    $exists = DB::table('academic_event_abstracts')
                        ->where('id', $abstractId)
                        ->where('academic_event_id', $session->academic_event_id)
                        ->exists();
                    if (! $exists) {
                        $errors["items.{$index}.academic_event_abstract_id"] = "{$row}번째 항목의 초록이 현재 행사에 속하지 않습니다.";
                    }
                }
                if (trim((string) ($item['title'] ?? '')) === '') {
                    $errors["items.{$index}.title"] = "{$row}번째 항목의 초록명/제목을 입력해주세요.";
                }
                if (trim((string) ($item['presenter'] ?? '')) === '') {
                    $errors["items.{$index}.presenter"] = "{$row}번째 항목의 발표자/연자를 입력해주세요.";
                }
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }
}
