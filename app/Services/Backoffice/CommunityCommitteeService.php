<?php

namespace App\Services\Backoffice;

use App\Models\CommunityCommittee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CommunityCommitteeService
{
    /** @return array<string,string> */
    public static function committeeTypeLabels(): array
    {
        return [
            'general' => '일반(회원 신청 가능)',
            'special' => '특별(관리자 전용)',
        ];
    }

    /** @return array<string,string> */
    public static function visibilityLabels(): array
    {
        return [
            'Y' => '노출',
            'N' => '미노출',
        ];
    }

    /** @return array<string,string> */
    public static function roleLabels(): array
    {
        return [
            'chairman' => '위원장',
            'secretary' => '간사',
            'member' => '위원',
        ];
    }

    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;

        $query = CommunityCommittee::query()
            ->withCount([
                'applications as applications_live_count',
                'committeeMembers as members_live_count',
            ]);
        $keyword = trim((string) $request->input('keyword'));
        if ($keyword !== '') {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        return $query->orderBy('name', 'asc')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}

