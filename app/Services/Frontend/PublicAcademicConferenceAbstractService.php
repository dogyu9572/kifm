<?php

namespace App\Services\Frontend;

use App\Models\AcademicEvent;
use App\Models\AcademicEventAbstract;
use App\Models\AcademicEventAbstractFile;
use App\Models\User;
use App\Support\CategoryOptions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PublicAcademicConferenceAbstractService
{
    public const MAX_FILES = 5;
    public const MAX_FILE_SIZE_KB = 10240;

    /** @return array<string, string> */
    public function presentationTypeLabels(): array
    {
        return CategoryOptions::labelsByGroupCode(CategoryOptions::ABSTRACT_PRESENTATION_TYPE_GROUP_CODE, [
            'oral' => '구연 발표',
            'poster' => '포스터 발표',
            'special' => '특별 강연',
        ]);
    }

    /** @return array<string, string> */
    public function activePresentationTypeLabels(AcademicEvent $event): array
    {
        $selected = array_values(array_filter((array) $event->presentation_types));
        $labels = $this->presentationTypeLabels();

        if ($selected === []) {
            return $labels;
        }

        return collect($labels)
            ->only($selected)
            ->all();
    }

    public function canSubmit(AcademicEvent $event): bool
    {
        if (! $event->abstract_start || ! $event->abstract_end) {
            return true;
        }

        $today = Carbon::today();

        return $today->gte($event->abstract_start) && $today->lte($event->abstract_end);
    }

    public function canModify(AcademicEvent $event): bool
    {
        $end = $event->abstract_revision_end ?: $event->abstract_end;
        if (! $end) {
            return true;
        }

        return Carbon::today()->lte($end);
    }

    /** @return Collection<int, AcademicEventAbstract> */
    public function memberAbstracts(AcademicEvent $event, User $user): Collection
    {
        return AcademicEventAbstract::query()
            ->with(['files', 'field'])
            ->where('academic_event_id', $event->id)
            ->where('member_id', $user->id)
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get();
    }

    public function findForLookup(AcademicEvent $event, ?User $user, ?int $lookupId): ?AcademicEventAbstract
    {
        if ($user?->role === 'user') {
            return $this->memberAbstracts($event, $user)->first();
        }

        if (! $lookupId || $lookupId < 1) {
            return null;
        }

        return AcademicEventAbstract::query()
            ->with(['files', 'field', 'member'])
            ->where('academic_event_id', $event->id)
            ->whereKey($lookupId)
            ->first();
    }

    public function findNonMemberAbstract(AcademicEvent $event, string $name, string $email, string $phone, string $password): ?AcademicEventAbstract
    {
        $phone = preg_replace('/\D+/', '', $phone);

        $abstracts = AcademicEventAbstract::query()
            ->with(['files', 'field'])
            ->where('academic_event_id', $event->id)
            ->whereNull('member_id')
            ->where('author_name', trim($name))
            ->where('author_email', trim($email))
            ->where('author_mobile', $phone)
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get();

        return $abstracts->first(
            fn (AcademicEventAbstract $abstract) => $abstract->lookup_password
                && Hash::check($password, $abstract->lookup_password)
        );
    }

    public function canAccess(AcademicEventAbstract $abstract, AcademicEvent $event, ?User $user, ?int $lookupId): bool
    {
        if ((int) $abstract->academic_event_id !== (int) $event->id) {
            return false;
        }

        if ($user?->role === 'user' && (int) $abstract->member_id === (int) $user->id) {
            return true;
        }

        return $lookupId !== null && $lookupId > 0 && (int) $abstract->id === $lookupId;
    }

    public function createForMember(AcademicEvent $event, User $user, array $data, array $uploads): AcademicEventAbstract
    {
        return DB::transaction(function () use ($event, $user, $data, $uploads) {
            $abstract = AcademicEventAbstract::query()->create([
                'academic_event_id' => $event->id,
                'member_id' => $user->id,
                'abstract_no' => $this->nextAbstractNo($event),
                'registered_by' => 'user',
                'status' => 'receipt',
                'file_receipt_status' => $this->hasUploads($uploads) ? 'not_received' : 'not_submitted',
                'author_name' => (string) $user->name,
                'author_name_en' => $user->name_en,
                'author_phone' => $user->workplace_phone,
                'author_mobile' => preg_replace('/\D+/', '', (string) ($user->phone_number ?: ($data['author_mobile'] ?? ''))),
                'author_email' => (string) ($data['author_email'] ?? $user->email),
                'title' => (string) $data['title'],
                'presentation_type' => (string) $data['presentation_type'],
                'academic_event_field_id' => $data['academic_event_field_id'] ?? null,
                'note' => $data['note'] ?? null,
                'submitted_at' => now(),
            ]);

            $this->storeUploadedFiles($abstract, $uploads);

            return $abstract->load(['files', 'field']);
        });
    }

    public function createForNonMember(AcademicEvent $event, array $data, array $uploads): AcademicEventAbstract
    {
        return DB::transaction(function () use ($event, $data, $uploads) {
            $abstract = AcademicEventAbstract::query()->create([
                'academic_event_id' => $event->id,
                'member_id' => null,
                'abstract_no' => $this->nextAbstractNo($event),
                'lookup_password' => Hash::make((string) $data['lookup_password']),
                'registered_by' => 'user',
                'status' => 'receipt',
                'file_receipt_status' => $this->hasUploads($uploads) ? 'not_received' : 'not_submitted',
                'author_name' => (string) $data['author_name'],
                'author_name_en' => $data['author_name_en'] ?? null,
                'author_phone' => preg_replace('/\D+/', '', (string) ($data['author_phone'] ?? '')),
                'author_mobile' => preg_replace('/\D+/', '', (string) $data['author_mobile']),
                'author_email' => (string) $data['author_email'],
                'title' => (string) $data['title'],
                'presentation_type' => (string) $data['presentation_type'],
                'academic_event_field_id' => $data['academic_event_field_id'] ?? null,
                'note' => $data['note'] ?? null,
                'submitted_at' => now(),
            ]);

            $this->storeUploadedFiles($abstract, $uploads);

            return $abstract->load(['files', 'field']);
        });
    }

    public function updateAbstract(AcademicEventAbstract $abstract, array $data, array $uploads, array $removeFileIds): AcademicEventAbstract
    {
        return DB::transaction(function () use ($abstract, $data, $uploads, $removeFileIds) {
            $abstract->update([
                'author_name' => (string) ($data['author_name'] ?? $abstract->author_name),
                'author_name_en' => $data['author_name_en'] ?? $abstract->author_name_en,
                'author_phone' => preg_replace('/\D+/', '', (string) ($data['author_phone'] ?? $abstract->author_phone)),
                'author_mobile' => preg_replace('/\D+/', '', (string) ($data['author_mobile'] ?? $abstract->author_mobile)),
                'author_email' => (string) ($data['author_email'] ?? $abstract->author_email),
                'title' => (string) $data['title'],
                'presentation_type' => (string) $data['presentation_type'],
                'academic_event_field_id' => $data['academic_event_field_id'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            $this->deleteFilesForAbstract($abstract, $removeFileIds);
            $this->storeUploadedFiles($abstract, $uploads);
            $abstract->update([
                'file_receipt_status' => $abstract->files()->exists() ? 'not_received' : 'not_submitted',
            ]);

            return $abstract->fresh(['files', 'field']);
        });
    }

    public function abstractSummary(?AcademicEventAbstract $abstract): ?array
    {
        if (! $abstract) {
            return null;
        }

        $mobile = preg_replace('/\D+/', '', (string) $abstract->author_mobile);

        return [
            'abstract_no' => $abstract->abstract_no ?: sprintf('ABS-%06d', $abstract->id),
            'submitted_at' => optional($abstract->submitted_at)->format('Y-m-d H:i:s') ?: '-',
            'author_name' => $abstract->author_name,
            'author_mobile' => $this->phoneDisplay($mobile),
            'author_email' => $abstract->author_email ?: '-',
            'title' => $abstract->title,
            'presentation_type' => $this->presentationTypeLabels()[$abstract->presentation_type] ?? $abstract->presentation_type,
            'field' => $abstract->field?->name ?: '-',
        ];
    }

    public function fileUrl(AcademicEventAbstractFile $file): string
    {
        return Storage::disk('public')->url($file->stored_path);
    }

    private function nextAbstractNo(AcademicEvent $event): string
    {
        AcademicEvent::query()->whereKey($event->id)->lockForUpdate()->first();

        $latest = AcademicEventAbstract::query()
            ->where('academic_event_id', $event->id)
            ->whereNotNull('abstract_no')
            ->lockForUpdate()
            ->pluck('abstract_no');

        $max = 0;
        foreach ($latest as $no) {
            if (preg_match('/-(\d+)$/', (string) $no, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return sprintf('%s-ABS-%05d', $event->folder_name, $max + 1);
    }

    /** @param array<int, UploadedFile> $uploads */
    private function storeUploadedFiles(AcademicEventAbstract $abstract, array $uploads): void
    {
        $uploads = array_values(array_filter($uploads, static fn ($file) => $file instanceof UploadedFile && $file->isValid()));
        if ($uploads === []) {
            return;
        }

        $allowed = self::MAX_FILES - $abstract->files()->count();
        if ($allowed <= 0) {
            return;
        }

        $dir = 'academic_event_abstracts/' . $abstract->id;
        foreach (array_slice($uploads, 0, $allowed) as $file) {
            $path = $file->store($dir, 'public');
            AcademicEventAbstractFile::query()->create([
                'academic_event_abstract_id' => $abstract->id,
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $path,
                'file_size' => $file->getSize() ?: 0,
            ]);
        }
    }

    /** @param list<int|string> $fileIds */
    private function deleteFilesForAbstract(AcademicEventAbstract $abstract, array $fileIds): void
    {
        $ids = array_values(array_unique(array_map(static fn ($id) => (int) $id, $fileIds)));
        $ids = array_values(array_filter($ids, static fn (int $id) => $id > 0));
        if ($ids === []) {
            return;
        }

        $abstract->files()->whereIn('id', $ids)->get()->each(static fn (AcademicEventAbstractFile $file) => $file->delete());
    }

    private function hasUploads(array $uploads): bool
    {
        return count(array_filter($uploads, static fn ($file) => $file instanceof UploadedFile && $file->isValid())) > 0;
    }

    private function phoneDisplay(string $phone): string
    {
        return match (strlen($phone)) {
            11 => substr($phone, 0, 3) . '-' . substr($phone, 3, 4) . '-' . substr($phone, 7),
            10 => substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6),
            default => $phone,
        };
    }
}
