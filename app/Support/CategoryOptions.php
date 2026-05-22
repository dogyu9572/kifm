<?php

namespace App\Support;

use App\Models\Category;

class CategoryOptions
{
    public const ABSTRACT_PRESENTATION_TYPE_GROUP_CODE = 'abstract_presentation_type';

    /** @param array<string, string> $fallback */
    public static function labelsByGroupCode(string $groupCode, array $fallback = []): array
    {
        $group = Category::query()
            ->whereNull('parent_id')
            ->where('depth', 0)
            ->where('code', $groupCode)
            ->where('is_active', true)
            ->first();

        if (! $group) {
            return $fallback;
        }

        $labels = Category::query()
            ->where('parent_id', $group->id)
            ->where('depth', 1)
            ->where('is_active', true)
            ->whereNotNull('code')
            ->orderByDesc('display_order')
            ->orderBy('id')
            ->get(['code', 'name'])
            ->mapWithKeys(static fn (Category $category) => [(string) $category->code => (string) $category->name])
            ->all();

        return $labels !== [] ? $labels : $fallback;
    }
}
