<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Popup extends Model
{
    use SoftDeletes;

    public const MENU_SCOPE_SITE = 'site';

    public const MENU_SCOPE_COMMITTEE = 'committee';

    /** 위원회 팝업에서 선택 가능한 게시판 slug → 라벨 */
    public const COMMITTEE_TARGET_BOARDS = [
        'community_committee_notices' => '공지사항',
        'community_committee_discussions' => '토론장',
        'community_committee_archive' => '자료실',
    ];

    protected $fillable = [
        'title',
        'menu_scope',
        'community_committee_id',
        'target_board_slug',
        'start_date',
        'end_date',
        'use_period',
        'width',
        'height',
        'position_top',
        'position_left',
        'url',
        'url_target',
        'popup_type',
        'popup_display_type',
        'popup_image',
        'popup_content',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'use_period' => 'boolean',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'community_committee_id' => 'integer',
    ];

    protected $attributes = [
        'url_target' => '_blank',
        'popup_type' => 'image',
        'popup_display_type' => 'normal',
    ];

    public function communityCommittee(): BelongsTo
    {
        return $this->belongsTo(CommunityCommittee::class, 'community_committee_id');
    }

    public function isCommitteeScope(): bool
    {
        return $this->menu_scope === self::MENU_SCOPE_COMMITTEE;
    }

    /**
     * 활성화된 팝업만 조회하는 스코프
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 게시 기간 내 팝업만 조회하는 스코프
     */
    public function scopeInPeriod($query)
    {
        return $query->where(function ($q) {
            $q->where('use_period', false)
              ->orWhere(function ($periodQuery) {
                  $now = now();
                  $periodQuery->where('use_period', true)
                             ->where('start_date', '<=', $now)
                             ->where('end_date', '>=', $now);
              });
        });
    }

    /**
     * 정렬된 팝업 조회하는 스코프
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'desc')->orderBy('created_at', 'desc');
    }

    public function scopeForMenuScope($query, string $menuScope)
    {
        return $query->where('menu_scope', $menuScope);
    }
}