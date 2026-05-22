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

        return $query->paginate(20)->withQueryString();
    }

    /** @return Collection<int, string> */
    public function contentTypes(User $user): Collection
    {
        return MemberBookmark::query()
            ->where('user_id', $user->id)
            ->whereNotNull('content_type')
            ->distinct()
            ->orderBy('content_type')
            ->pluck('content_type');
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
}
