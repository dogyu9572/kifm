<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

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

    public static function todayHideCookieName(int $popupId): string
    {
        return 'popup_hide_'.$popupId;
    }

    /**
     * 프론트에서 설정한 "오늘 하루 보지 않기" 쿠키로 숨김 여부
     * (과거 스크립트가 true 문자열로 저장한 경우도 인식)
     */
    public static function isHiddenByTodayCookie(int $popupId): bool
    {
        $name = self::todayHideCookieName($popupId);
        $v = request()->cookie($name);
        if (($v === null || $v === '') && isset($_COOKIE[$name]) && is_string($_COOKIE[$name])) {
            $v = $_COOKIE[$name];
        }
        if ($v === null || $v === '') {
            return false;
        }

        return in_array((string) $v, ['1', 'true'], true);
    }

    /**
     * 산하위원회 특정 게시판 화면에 노출할 위원회 팝업(활성·기간·쿠키 반영)
     */
    public static function activeCommitteePopupsForBoard(int $communityCommitteeId, string $boardSlug): Collection
    {
        if ($boardSlug === '' || ! isset(self::COMMITTEE_TARGET_BOARDS[$boardSlug])) {
            return collect();
        }

        return static::query()
            ->select([
                'id',
                'title',
                'popup_type',
                'popup_display_type',
                'popup_image',
                'popup_content',
                'url',
                'url_target',
                'width',
                'height',
                'position_top',
                'position_left',
            ])
            ->forMenuScope(self::MENU_SCOPE_COMMITTEE)
            ->where('community_committee_id', $communityCommitteeId)
            ->where('target_board_slug', $boardSlug)
            ->active()
            ->inPeriod()
            ->ordered()
            ->get()
            ->filter(static fn (self $popup): bool => ! self::isHiddenByTodayCookie((int) $popup->id));
    }
}