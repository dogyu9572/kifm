<?php

namespace App\Services\Frontend;

use App\Models\MemberMenuFavorite;
use App\Models\User;
use Illuminate\Support\Collection;

class MypageFavoriteMenuService
{
    /** @return array<int, array{title: string, items: array<int, array{code: string, name: string, url: string}>}> */
    public function menuGroups(): array
    {
        return config('mypage_menus.groups', []);
    }

    public function maxFavorites(): int
    {
        return (int) config('mypage_menus.max_favorites', 6);
    }

    /** @return Collection<int, MemberMenuFavorite> */
    public function savedForUser(User $user): Collection
    {
        return MemberMenuFavorite::query()
            ->where('user_id', $user->id)
            ->orderBy('sort_order')
            ->get();
    }

    /** @return array<int, string> */
    public function savedCodesForUser(User $user): array
    {
        return $this->savedForUser($user)->pluck('menu_code')->all();
    }

    /**
     * @param  list<string>  $menuCodes
     */
    public function sync(User $user, array $menuCodes): void
    {
        $max = $this->maxFavorites();
        $menuCodes = array_values(array_unique(array_filter($menuCodes)));
        if (count($menuCodes) > $max) {
            throw new \RuntimeException("즐겨찾는 메뉴는 최대 {$max}개까지 저장할 수 있습니다.");
        }

        $catalog = $this->catalogByCode();

        MemberMenuFavorite::query()->where('user_id', $user->id)->delete();

        $order = 1;
        foreach ($menuCodes as $code) {
            if (! isset($catalog[$code])) {
                continue;
            }
            $item = $catalog[$code];
            MemberMenuFavorite::query()->create([
                'user_id' => $user->id,
                'menu_code' => $code,
                'menu_name_snapshot' => $item['name'],
                'menu_url_snapshot' => $item['url'],
                'sort_order' => $order++,
            ]);
        }
    }

    /** @return array<string, array{name: string, url: string}> */
    private function catalogByCode(): array
    {
        $map = [];
        foreach ($this->menuGroups() as $group) {
            foreach ($group['items'] ?? [] as $item) {
                $map[$item['code']] = [
                    'name' => $item['name'],
                    'url' => $item['url'],
                ];
            }
        }

        return $map;
    }
}
