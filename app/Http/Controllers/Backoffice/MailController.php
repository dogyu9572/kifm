<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailRequest;
use App\Models\AddressBook;
use App\Models\Mail;
use App\Models\SocietyExecutive;
use App\Models\User;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function index(Request $request)
    {
        $query = Mail::query();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('date_type') && $request->filled('start_date')) {
            $dateColumn = $request->string('date_type') === 'sent_at' ? 'sent_at' : 'created_at';
            $query->whereDate($dateColumn, '>=', $request->string('start_date'));
        }

        if ($request->filled('date_type') && $request->filled('end_date')) {
            $dateColumn = $request->string('date_type') === 'sent_at' ? 'sent_at' : 'created_at';
            $query->whereDate($dateColumn, '<=', $request->string('end_date'));
        }

        if ($request->filled('search_field') && $request->filled('keyword')) {
            $fieldMap = [
                'title' => 'subject',
                'writer' => 'sender_name',
                'body' => 'body',
            ];
            $field = $fieldMap[$request->string('search_field')->toString()] ?? 'subject';
            $query->where($field, 'like', '%'.trim((string) $request->input('keyword')).'%');
        }

        $mails = $query->latest()->paginate(20)->withQueryString();

        return view('backoffice.mail.index', compact('mails'));
    }

    public function create()
    {
        $addressBooks = AddressBook::query()->orderBy('name')->get(['id', 'name', 'member_count']);
        $members = User::query()->whereNull('withdrawn_at')->orderBy('name')->limit(200)->get(['id', 'name', 'email']);
        $executives = SocietyExecutive::query()->orderBy('name')->limit(200)->get(['id', 'name', 'email']);

        return view('backoffice.mail.create', compact('addressBooks', 'members', 'executives'));
    }

    public function store(MailRequest $request)
    {
        $payload = $request->validated();
        $mail = Mail::create($this->buildMailPayload($payload));
        $this->syncRecipients($mail, $payload);

        return redirect()
            ->route('backoffice.mails.index')
            ->with('success', '메일이 저장되었습니다.');
    }

    public function edit(Mail $mail)
    {
        $mail->load('recipients');
        $addressBooks = AddressBook::query()->orderBy('name')->get(['id', 'name', 'member_count']);
        $members = User::query()->whereNull('withdrawn_at')->orderBy('name')->limit(200)->get(['id', 'name', 'email']);
        $executives = SocietyExecutive::query()->orderBy('name')->limit(200)->get(['id', 'name', 'email']);
        $selectedAddressBooks = $mail->recipients->where('source_type', 'ADDRESSBOOK')->pluck('source_id')->filter()->values()->all();
        $selectedMemberIds = $mail->recipients->where('source_type', 'MEMBER')->pluck('source_id')->filter()->values()->all();
        $selectedExecutiveIds = $mail->recipients->where('source_type', 'EXECUTIVE')->pluck('source_id')->filter()->values()->all();

        return view('backoffice.mail.edit', compact(
            'mail',
            'addressBooks',
            'members',
            'executives',
            'selectedAddressBooks',
            'selectedMemberIds',
            'selectedExecutiveIds'
        ));
    }

    public function update(MailRequest $request, Mail $mail)
    {
        $payload = $request->validated();
        $mail->update($this->buildMailPayload($payload));
        $this->syncRecipients($mail, $payload);

        return redirect()
            ->route('backoffice.mails.index')
            ->with('success', '메일이 수정되었습니다.');
    }

    public function destroy(Mail $mail)
    {
        $mail->delete();

        return redirect()
            ->route('backoffice.mails.index')
            ->with('success', '메일이 삭제되었습니다.');
    }

    public function copy(Mail $mail)
    {
        $newMail = $mail->replicate();
        $newMail->status = 'DRAFT';
        $newMail->sent_at = null;
        $newMail->scheduled_at = null;
        $newMail->schedule_enabled = false;
        $newMail->save();

        foreach ($mail->recipients as $recipient) {
            $newMail->recipients()->create($recipient->only([
                'source_type',
                'source_id',
                'recipient_name',
                'recipient_email',
            ]));
        }

        return redirect()
            ->route('backoffice.mails.edit', $newMail)
            ->with('success', '메일이 복사되었습니다.');
    }

    public function cancelSchedule(Mail $mail)
    {
        $mail->update([
            'status' => 'DRAFT',
            'schedule_enabled' => false,
            'scheduled_at' => null,
        ]);

        return redirect()
            ->route('backoffice.mails.index')
            ->with('success', '예약 발송이 취소되었습니다.');
    }

    private function buildMailPayload(array $payload): array
    {
        $addressBookIds = $this->decodeIds($payload['selected_address_books'] ?? null);
        $memberIds = $this->decodeIds($payload['selected_member_ids'] ?? null);
        $executiveIds = $this->decodeIds($payload['selected_executive_ids'] ?? null);
        $sendMode = $payload['submit_action'] === 'send';
        $isScheduled = $sendMode && (bool) ($payload['schedule_enabled'] ?? false);

        return [
            'sender_name' => $payload['sender_name'] ?? null,
            'sender_email' => $payload['sender_email'] ?? null,
            'recipient_type' => $payload['recipient_type'],
            'member_grade' => $payload['member_grade'] ?? null,
            'exclude_emails' => $payload['exclude_emails'] ?? null,
            'schedule_enabled' => $isScheduled,
            'scheduled_at' => $isScheduled ? ($payload['scheduled_at'] ?? null) : null,
            'mail_type' => $payload['mail_type'],
            'subject' => $payload['subject'] ?? null,
            'body' => $payload['body'] ?? null,
            'status' => $sendMode ? ($isScheduled ? 'SCHEDULED' : 'SENT') : 'DRAFT',
            'sent_at' => $sendMode && ! $isScheduled ? now() : null,
            'recipient_count' => $this->calculateRecipientCount($payload['recipient_type'], $addressBookIds, $memberIds, $executiveIds),
            'created_by' => auth()->id(),
        ];
    }

    private function calculateRecipientCount(string $recipientType, array $addressBookIds, array $memberIds, array $executiveIds): int
    {
        return match ($recipientType) {
            'addressbook' => count($addressBookIds),
            'specific' => count($memberIds),
            'executive' => count($executiveIds),
            default => (int) User::query()->whereNull('withdrawn_at')->count(),
        };
    }

    private function syncRecipients(Mail $mail, array $payload): void
    {
        $addressBookIds = $this->decodeIds($payload['selected_address_books'] ?? null);
        $memberIds = $this->decodeIds($payload['selected_member_ids'] ?? null);
        $executiveIds = $this->decodeIds($payload['selected_executive_ids'] ?? null);
        $mail->recipients()->delete();

        if ($payload['recipient_type'] === 'addressbook') {
            $books = AddressBook::whereIn('id', $addressBookIds)->get();
            foreach ($books as $book) {
                $mail->recipients()->create([
                    'source_type' => 'ADDRESSBOOK',
                    'source_id' => $book->id,
                    'recipient_name' => $book->name,
                    'recipient_email' => $book->name.'@address-book.local',
                ]);
            }

            return;
        }

        if ($payload['recipient_type'] === 'specific') {
            $users = User::whereIn('id', $memberIds)->get();
            foreach ($users as $user) {
                $mail->recipients()->create([
                    'source_type' => 'MEMBER',
                    'source_id' => $user->id,
                    'recipient_name' => $user->name,
                    'recipient_email' => (string) ($user->email ?? ''),
                ]);
            }

            return;
        }

        if ($payload['recipient_type'] === 'executive') {
            $executives = SocietyExecutive::whereIn('id', $executiveIds)->get();
            foreach ($executives as $executive) {
                $mail->recipients()->create([
                    'source_type' => 'EXECUTIVE',
                    'source_id' => $executive->id,
                    'recipient_name' => $executive->name,
                    'recipient_email' => (string) ($executive->email ?? ''),
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
