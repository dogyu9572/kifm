<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\SmsRequest;
use App\Models\AddressBook;
use App\Models\SmsMessage;
use App\Models\User;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function index(Request $request)
    {
        $query = SmsMessage::query();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('sent_at', '>=', $request->string('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('sent_at', '<=', $request->string('end_date'));
        }

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->input('keyword'));
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('subject', 'like', '%'.$keyword.'%')
                    ->orWhere('body', 'like', '%'.$keyword.'%');
            });
        }

        $smsMessages = $query->latest()->paginate(20)->withQueryString();

        return view('backoffice.sms.index', compact('smsMessages'));
    }

    public function create()
    {
        $addressBooks = AddressBook::query()->orderBy('name')->get(['id', 'name', 'member_count']);
        $members = User::query()->whereNull('withdrawn_at')->orderBy('name')->limit(200)->get(['id', 'name', 'phone_number']);

        return view('backoffice.sms.create', compact('addressBooks', 'members'));
    }

    public function store(SmsRequest $request)
    {
        $payload = $request->validated();
        $smsMessage = SmsMessage::create($this->buildSmsPayload($payload));
        $this->syncRecipients($smsMessage, $payload);

        return redirect()
            ->route('backoffice.sms.index')
            ->with('success', '문자 발송 정보가 저장되었습니다.');
    }

    public function edit(SmsMessage $sms)
    {
        $sms->load('recipients');
        $addressBooks = AddressBook::query()->orderBy('name')->get(['id', 'name', 'member_count']);
        $members = User::query()->whereNull('withdrawn_at')->orderBy('name')->limit(200)->get(['id', 'name', 'phone_number']);

        return view('backoffice.sms.edit', ['smsMessage' => $sms, 'addressBooks' => $addressBooks, 'members' => $members]);
    }

    public function update(SmsRequest $request, SmsMessage $sms)
    {
        $payload = $request->validated();
        $sms->update($this->buildSmsPayload($payload));
        $this->syncRecipients($sms, $payload);

        return redirect()
            ->route('backoffice.sms.index')
            ->with('success', '문자 발송 정보가 수정되었습니다.');
    }

    public function destroy(SmsMessage $sms)
    {
        $sms->delete();

        return redirect()
            ->route('backoffice.sms.index')
            ->with('success', '문자 발송 정보가 삭제되었습니다.');
    }

    public function copy(SmsMessage $sms)
    {
        $newSms = $sms->replicate();
        $newSms->status = 'DRAFT';
        $newSms->sent_at = null;
        $newSms->scheduled_at = null;
        $newSms->schedule_enabled = false;
        $newSms->save();

        foreach ($sms->recipients as $recipient) {
            $newSms->recipients()->create($recipient->only([
                'source_type',
                'source_id',
                'recipient_name',
                'recipient_phone',
            ]));
        }

        return redirect()
            ->route('backoffice.sms.edit', $newSms)
            ->with('success', '문자 발송 정보가 복사되었습니다.');
    }

    public function cancelSchedule(SmsMessage $sms)
    {
        $sms->update([
            'status' => 'DRAFT',
            'schedule_enabled' => false,
            'scheduled_at' => null,
        ]);

        return redirect()
            ->route('backoffice.sms.index')
            ->with('success', '문자 예약 발송이 취소되었습니다.');
    }

    private function buildSmsPayload(array $payload): array
    {
        $addressBookIds = $this->decodeIds($payload['selected_address_books'] ?? null);
        $memberIds = $this->decodeIds($payload['selected_member_ids'] ?? null);
        $sendMode = $payload['submit_action'] === 'send';
        $isScheduled = $sendMode && (bool) ($payload['schedule_enabled'] ?? false);
        $body = (string) ($payload['body'] ?? '');
        $byteSize = $this->calculateByteSize($body);
        $smsType = $byteSize > 80 ? 'LMS' : 'SMS';

        return [
            'sender_number' => $payload['sender_number'] ?? null,
            'recipient_type' => $payload['recipient_type'],
            'member_grade' => $payload['member_grade'] ?? null,
            'exclude_phones' => $payload['exclude_phones'] ?? null,
            'schedule_enabled' => $isScheduled,
            'scheduled_at' => $isScheduled ? ($payload['scheduled_at'] ?? null) : null,
            'sms_type' => $smsType,
            'subject' => $payload['subject'] ?? null,
            'body' => $body,
            'byte_size' => $byteSize,
            'status' => $sendMode ? ($isScheduled ? 'RESERVED' : 'DONE') : 'DRAFT',
            'sent_at' => $sendMode && ! $isScheduled ? now() : null,
            'recipient_count' => $this->calculateRecipientCount($payload['recipient_type'], $addressBookIds, $memberIds),
            'created_by' => auth()->id(),
        ];
    }

    private function calculateByteSize(string $text): int
    {
        $byteSize = 0;
        foreach (mb_str_split($text) as $char) {
            $byteSize += strlen($char) === 1 ? 1 : 2;
        }

        return $byteSize;
    }

    private function calculateRecipientCount(string $recipientType, array $addressBookIds, array $memberIds): int
    {
        return match ($recipientType) {
            'addressbook' => count($addressBookIds),
            'specific' => count($memberIds),
            default => (int) User::query()->whereNull('withdrawn_at')->count(),
        };
    }

    private function syncRecipients(SmsMessage $smsMessage, array $payload): void
    {
        $addressBookIds = $this->decodeIds($payload['selected_address_books'] ?? null);
        $memberIds = $this->decodeIds($payload['selected_member_ids'] ?? null);
        $smsMessage->recipients()->delete();

        if ($payload['recipient_type'] === 'addressbook') {
            $books = AddressBook::whereIn('id', $addressBookIds)->get();
            foreach ($books as $book) {
                $smsMessage->recipients()->create([
                    'source_type' => 'ADDRESSBOOK',
                    'source_id' => $book->id,
                    'recipient_name' => $book->name,
                    'recipient_phone' => '00000000000',
                ]);
            }

            return;
        }

        if ($payload['recipient_type'] === 'specific') {
            $users = User::whereIn('id', $memberIds)->get();
            foreach ($users as $user) {
                $smsMessage->recipients()->create([
                    'source_type' => 'MEMBER',
                    'source_id' => $user->id,
                    'recipient_name' => $user->name,
                    'recipient_phone' => (string) User::normalizePhone($user->phone_number),
                ]);
            }
        }
    }

    private function decodeIds(mixed $value): array
    {
        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        if (! is_array($decoded)) {
            return [];
        }

        return array_values(array_filter(array_map(static fn ($id) => is_numeric($id) ? (int) $id : null, $decoded)));
    }
}
