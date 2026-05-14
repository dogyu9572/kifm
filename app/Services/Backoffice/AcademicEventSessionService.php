<?php

namespace App\Services\Backoffice;

use App\Models\AcademicEvent;
use App\Models\AcademicEventSession;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AcademicEventSessionService
{
    public function create(AcademicEvent $event, array $data): AcademicEventSession
    {
        $this->validateTimes($event, $data);

        $session = new AcademicEventSession();
        $session->academic_event_id = $event->id;
        $this->fillSession($session, $data);
        $session->save();

        return $session->fresh();
    }

    public function update(AcademicEventSession $session, array $data): AcademicEventSession
    {
        $this->validateTimes($session->event, $data, $session->id);
        $this->fillSession($session, $data);
        $session->save();

        return $session->fresh();
    }

    public function delete(AcademicEventSession $session): void
    {
        $session->delete();
    }

    protected function fillSession(AcademicEventSession $session, array $data): void
    {
        $session->fill([
            'name' => $data['name'],
            'category' => $data['category'] ?? null,
            'session_date' => $data['session_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'description' => $data['description'] ?? null,
            'chair_speakers' => $data['chair_speakers'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? $session->sort_order ?? 0),
        ]);
    }

    protected function validateTimes(AcademicEvent $event, array $data, ?int $ignoreSessionId = null): void
    {
        $start = Carbon::parse($data['session_date'] . ' ' . $data['start_time']);
        $end = Carbon::parse($data['session_date'] . ' ' . $data['end_time']);
        if ($end->lte($start)) {
            throw ValidationException::withMessages([
                'end_time' => '종료 시간은 시작 시간보다 이후여야 합니다.',
            ]);
        }

        if ($event->start_at && $event->end_at) {
            $es = $event->start_at->copy()->startOfDay();
            $ee = $event->end_at->copy()->endOfDay();
            $sd = Carbon::parse($data['session_date'])->startOfDay();
            if ($sd->lt($es) || $sd->gt($ee)) {
                throw ValidationException::withMessages([
                    'session_date' => '세션 날짜는 행사 개최 기간 안에 있어야 합니다.',
                ]);
            }
        }

        $q = AcademicEventSession::query()
            ->where('academic_event_id', $event->id)
            ->whereDate('session_date', $data['session_date']);
        if ($ignoreSessionId) {
            $q->whereKeyNot($ignoreSessionId);
        }
        $others = $q->get(['id', 'session_date', 'start_time', 'end_time']);
        $dateStr = $data['session_date'];
        $newStart = Carbon::parse($dateStr . ' ' . $data['start_time']);
        $newEnd = Carbon::parse($dateStr . ' ' . $data['end_time']);
        foreach ($others as $o) {
            $d = $o->session_date instanceof Carbon
                ? $o->session_date->format('Y-m-d')
                : (string) $o->session_date;
            $os = Carbon::parse($d . ' ' . $o->start_time);
            $oe = Carbon::parse($d . ' ' . $o->end_time);
            if ($newEnd->lte($os) || $newStart->gte($oe)) {
                continue;
            }
            throw ValidationException::withMessages([
                'start_time' => '동일 날짜에 겹치는 세션 시간이 있습니다.',
            ]);
        }
    }
}
