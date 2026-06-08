<?php

namespace App\Services\Frontend;

use App\Models\MemberBookmark;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MypageBookmarkService
{
    public function paginate(User $user, Request $request): LengthAwarePaginator
    {
        $query = MemberBookmark::query()
            ->where('user_id', $user->id)
            ->orderByDesc('bookmarked_at');

        if ($request->filled('content_type') && $request->get('content_type') !== 'all') {
            $query->where('content_type', (string) $request->get('content_type'));
        }

        $keyword = trim((string) $request->get('keyword', ''));
        if ($keyword !== '') {
            $query->where('snapshot_title', 'like', '%'.$keyword.'%');
        }

        $bookmarks = $query->paginate(20)->withQueryString();
        $bookmarks->getCollection()->transform(function (MemberBookmark $bookmark): MemberBookmark {
            $bookmark->display_menu_label = $this->displayMenuLabel(
                $bookmark->content_type,
                $bookmark->snapshot_menu_label,
            );

            return $bookmark;
        });

        return $bookmarks;
    }

    /** @return Collection<int, array{value:string,label:string}> */
    public function contentTypes(User $user): Collection
    {
        return MemberBookmark::query()
            ->where('user_id', $user->id)
            ->whereNotNull('content_type')
            ->distinct()
            ->orderBy('content_type')
            ->pluck('content_type')
            ->map(fn (string $contentType): array => [
                'value' => $contentType,
                'label' => $this->displayMenuLabel($contentType),
            ]);
    }

    public function isBookmarked(User $user, string $contentType, int $contentId): bool
    {
        return MemberBookmark::query()
            ->where('user_id', $user->id)
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->exists();
    }

    public function toggle(User $user, string $contentType, int $contentId, ?string $title, ?string $menuLabel, ?string $url): bool
    {
        $existing = MemberBookmark::query()
            ->where('user_id', $user->id)
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        MemberBookmark::query()->create([
            'user_id' => $user->id,
            'content_type' => $contentType,
            'content_id' => $contentId,
            'snapshot_title' => $title,
            'snapshot_menu_label' => $menuLabel,
            'snapshot_url' => $url,
            'bookmarked_at' => now(),
        ]);

        return true;
    }

    public function destroyIds(User $user, array $ids): int
    {
        return MemberBookmark::query()
            ->where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->delete();
    }

    private function displayMenuLabel(?string $contentType, ?string $snapshotMenuLabel = null): string
    {
        $contentType = trim((string) $contentType);
        $snapshotMenuLabel = trim((string) $snapshotMenuLabel);

        if ($snapshotMenuLabel !== '' && ! $this->isTechnicalLabel($snapshotMenuLabel, $contentType)) {
            return $snapshotMenuLabel;
        }

        return $this->contentTypeLabels()[$contentType] ?? $this->humanizeContentType($contentType);
    }

    private function isTechnicalLabel(string $label, string $contentType): bool
    {
        if ($label === $contentType) {
            return true;
        }

        return preg_match('/^[a-z0-9_\\-\\/\\.]+$/', $label) === 1;
    }

    /** @return array<string, string> */
    private function contentTypeLabels(): array
    {
        return [
            'academic_archive' => '학술 자료실',
            'academic_event' => '학술대회',
            'academic_event_conference_static' => '학술대회',
            'academic_event_training_course' => '연수강좌',
            'community_committee_archive' => '자료실',
            'community_committee_discussions' => '토론장',
            'community_committee_notices' => '공지사항',
            'general_archive' => '일반 자료실',
            'member_archive' => '회원 자료실',
            'member_square_album' => '회원 광장 앨범',
            'member_square_notices' => '회원 광장 공지사항',
            'other_notices' => '위원회 공지',
            'conference' => '학술대회',
            'training_course' => '연수강좌',
            'archives' => '자료실',
            'notice' => '공지사항',
            'discussion' => '토론장',
        ];
    }

    private function humanizeContentType(string $contentType): string
    {
        if ($contentType === '') {
            return '-';
        }

        return str_replace(['_', '-'], ' ', $contentType);
    }
}
