@props([
    'contentType',
    'contentId',
    'title',
    'menuLabel',
    'url' => null,
    'label' => '이 게시글을 북마크에 추가',
])
@inject('bookmarkService', 'App\Services\Frontend\MypageBookmarkService')
@php
    $bookmarkUser = auth()->user();
    $bookmarkContentId = (int) $contentId;
    $bookmarkContentType = (string) $contentType;
    $isBookmarked = $bookmarkUser && $bookmarkUser->role === 'user' && $bookmarkContentId > 0
        ? $bookmarkService->isBookmarked($bookmarkUser, $bookmarkContentType, $bookmarkContentId)
        : false;
    $bookmarkUrl = $url ?: url()->current();
@endphp
<button
    type="button"
    class="bookmark{{ $isBookmarked ? ' on' : '' }}"
    aria-label="{{ $isBookmarked ? '북마크 취소' : $label }}"
    aria-pressed="{{ $isBookmarked ? 'true' : 'false' }}"
    data-bookmark-toggle
    data-bookmark-url="{{ route('mypage.bookmark.toggle') }}"
    data-login-url="{{ route('member.login') }}"
    data-default-label="{{ $label }}"
    data-content-type="{{ $bookmarkContentType }}"
    data-content-id="{{ $bookmarkContentId }}"
    data-title="{{ $title }}"
    data-menu-label="{{ $menuLabel }}"
    data-url="{{ $bookmarkUrl }}"
></button>
