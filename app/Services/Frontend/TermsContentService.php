<?php

namespace App\Services\Frontend;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TermsContentService
{
    public function getSinglePageContent(string $slug): ?object
    {
        $table = 'board_'.$slug;
        if (! Schema::hasTable($table)) {
            return null;
        }

        return DB::table($table)
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first(['id', 'title', 'content', 'updated_at']);
    }
}
