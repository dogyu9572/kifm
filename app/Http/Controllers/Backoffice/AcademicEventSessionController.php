<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\AcademicEventSessionRequest;
use App\Models\AcademicEvent;
use App\Models\AcademicEventSession;
use App\Services\Backoffice\AcademicEventSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcademicEventSessionController extends Controller
{
    public function __construct(
        protected AcademicEventSessionService $sessionService
    ) {}

    /** 학술행사 수정 화면의 세션 탭으로 이동하는 URL */
    protected function editEventSessionsTabUrl(AcademicEvent $event): string
    {
        return route('backoffice.academic-events.edit', [
            'academic_event' => $event,
            'tab' => 'sessions',
        ]);
    }

    public function create(Request $request, AcademicEvent $academic_event)
    {
        return view('backoffice.academic-events.sessions.create', [
            'event' => $academic_event,
            'session' => new AcademicEventSession([
                'session_date' => $academic_event->start_at?->toDateString() ?? now()->toDateString(),
                'start_time' => '09:00',
                'end_time' => '10:00',
                'sort_order' => ((int) $academic_event->sessions()->max('sort_order')) + 1,
            ]),
            'categoryLabels' => [
                'oral' => '구연 발표',
                'poster' => '포스터 발표',
                'special' => '특별 강연',
                'symposium' => '심포지엄',
            ],
            'cancelUrl' => $this->editEventSessionsTabUrl($academic_event),
        ]);
    }

    public function store(AcademicEventSessionRequest $request, AcademicEvent $academic_event): RedirectResponse
    {
        $this->sessionService->create($academic_event, $request->validated());

        return redirect()->to($this->editEventSessionsTabUrl($academic_event))
            ->with('success', '세션이 등록되었습니다.');
    }

    public function edit(Request $request, AcademicEvent $academic_event, AcademicEventSession $academic_event_session)
    {
        if ($academic_event_session->academic_event_id !== $academic_event->id) {
            abort(404);
        }

        return view('backoffice.academic-events.sessions.edit', [
            'event' => $academic_event,
            'session' => $academic_event_session,
            'categoryLabels' => [
                'oral' => '구연 발표',
                'poster' => '포스터 발표',
                'special' => '특별 강연',
                'symposium' => '심포지엄',
            ],
            'cancelUrl' => $this->editEventSessionsTabUrl($academic_event),
        ]);
    }

    public function update(AcademicEventSessionRequest $request, AcademicEvent $academic_event, AcademicEventSession $academic_event_session): RedirectResponse
    {
        if ($academic_event_session->academic_event_id !== $academic_event->id) {
            abort(404);
        }
        $this->sessionService->update($academic_event_session, $request->validated());

        return redirect()->to($this->editEventSessionsTabUrl($academic_event))
            ->with('success', '세션이 수정되었습니다.');
    }

    public function destroy(AcademicEvent $academic_event, AcademicEventSession $academic_event_session): RedirectResponse
    {
        if ($academic_event_session->academic_event_id !== $academic_event->id) {
            abort(404);
        }
        $this->sessionService->delete($academic_event_session);

        return redirect()->to($this->editEventSessionsTabUrl($academic_event))
            ->with('success', '세션이 삭제되었습니다.');
    }
}
