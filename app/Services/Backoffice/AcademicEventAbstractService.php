<?php

namespace App\Services\Backoffice;

use App\Models\AcademicEventAbstract;
use App\Models\AcademicEventAbstractFile;
use App\Support\CategoryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AcademicEventAbstractService
{
    public const MAX_FILES_PER_ABSTRACT = 5;

    public const MAX_FILE_SIZE_KB = 10240;

    /** @return array<string, string> */
    public static function statusLabels(): array
    {
        return [
            'receipt' => '접수',
            'confirmed' => '확인 완료',
        ];
    }

    /** @return array<string, string> */
    public static function fileReceiptLabels(): array
    {
        return [
            'received' => '수령 완료',
            'not_received' => '미수령',
            'not_submitted' => '미제출',
        ];
    }

    /** @return array<string, string> */
    public static function registeredByLabels(): array
    {
        return [
            'user' => '사용자 등록',
            'admin' => '관리자 등록',
        ];
    }

    /** 목록 테이블용 (프로토타입: 직접/대리) */
    /** @return array<string, string> */
    public static function registeredByListLabels(): array
    {
        return [
            'user' => '직접 등록',
            'admin' => '대리 등록',
        ];
    }

    /** @return array<string, string> */
    public static function presentationTypeLabels(): array
    {
        return CategoryOptions::labelsByGroupCode(CategoryOptions::ABSTRACT_PRESENTATION_TYPE_GROUP_CODE, [
            'oral' => '구연 발표',
            'poster' => '포스터 발표',
            'special' => '특별 강연',
        ]);
    }

    public function paginateIndex(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;

        $query = $this->indexFilterQuery($request)->with(['event', 'files']);

        return $query->paginate($perPage)->withQueryString();
    }

    public function indexFilterQuery(Request $request): Builder
    {
        $query = AcademicEventAbstract::query()
            ->orderByDesc('submitted_at')
            ->orderByDesc('id');

        if ($request->filled('academic_event_id')) {
            $query->where('academic_event_id', (int) $request->get('academic_event_id'));
        }
        if ($request->filled('presentation_type') && $request->get('presentation_type') !== 'all') {
            $query->where('presentation_type', (string) $request->get('presentation_type'));
        }
        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('status', (string) $request->get('status'));
        }
        $keyword = trim((string) $request->get('search_keyword', ''));
        if ($keyword !== '') {
            $like = '%' . $keyword . '%';
            $query->where(function (Builder $q) use ($like) {
                $q->where('title', 'like', $like)
                    ->orWhere('author_name', 'like', $like)
                    ->orWhere('author_name_en', 'like', $like);
            });
        }

        return $query;
    }

    /**
     * @param  list<int|string>  $ids
     * @return int 삭제된 행 수
     */
    public function deleteMany(array $ids): int
    {
        $unique = array_values(array_unique(array_map(static fn ($id) => (int) $id, $ids)));
        $unique = array_values(array_filter($unique, static fn (int $id) => $id > 0));
        if ($unique === []) {
            return 0;
        }

        return (int) DB::transaction(function () use ($unique) {
            $count = 0;
            $models = AcademicEventAbstract::query()->whereIn('id', $unique)->get();
            foreach ($models as $model) {
                if (! $this->canDeleteAbstract($model)) {
                    continue;
                }
                $model->delete();
                $count++;
            }

            return $count;
        });
    }

    /**
     * @param  array<int, UploadedFile>  $uploads
     */
    public function storeUploadedFiles(AcademicEventAbstract $abstract, array $uploads): void
    {
        $existing = $abstract->files()->count();
        $uploads = array_values(array_filter($uploads, static fn ($f) => $f instanceof UploadedFile && $f->isValid()));
        if ($uploads === []) {
            return;
        }

        $allowed = self::MAX_FILES_PER_ABSTRACT - $existing;
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

    /**
     * @param  list<int>  $fileIds
     */
    public function deleteFilesForAbstract(AcademicEventAbstract $abstract, array $fileIds): void
    {
        $ids = array_values(array_unique(array_map(static fn ($id) => (int) $id, $fileIds)));
        $ids = array_values(array_filter($ids, static fn (int $id) => $id > 0));
        if ($ids === []) {
            return;
        }

        $abstract->files()->whereIn('id', $ids)->get()->each(static fn (AcademicEventAbstractFile $f) => $f->delete());
    }

    public function publicFileUrl(AcademicEventAbstractFile $file): string
    {
        return asset('storage/' . ltrim($file->stored_path, '/'));
    }

    /**
     * 심사 배정 등 연동 후 삭제 제한을 둘 수 있는 확장 훅. 현재는 항상 허용.
     */
    public function canDeleteAbstract(AcademicEventAbstract $abstract): bool
    {
        if (! Schema::hasTable('academic_event_session_items')) {
            return true;
        }

        return ! $abstract->sessionItems()->exists();
    }

    /**
     * 행사별 발표 분야 옵션 (프론트 연동용 JSON).
     *
     * @return array<int, list<array{id:int,name:string}>>
     */
    public function eventFieldsMap(): array
    {
        $map = [];
        foreach (\App\Models\AcademicEvent::query()->with(['fields' => fn ($q) => $q->orderBy('sort_order')])->orderByDesc('year')->orderByDesc('id')->get() as $event) {
            $map[$event->id] = $event->fields->map(static fn ($f) => [
                'id' => $f->id,
                'name' => $f->name,
            ])->all();
        }

        return $map;
    }

    public static function shortTitle(string $title, int $max = 80): string
    {
        return Str::limit($title, $max);
    }
}
