@props(['paginator', 'embedded' => false, 'windowSize' => null])

@php
    $lastPage = $paginator->lastPage();
    $safeWindowSize = $windowSize !== null ? max((int) $windowSize, 1) : $lastPage;
    $currentPage = $paginator->currentPage();
    $startPage = (int) (floor(($currentPage - 1) / $safeWindowSize) * $safeWindowSize) + 1;
    $endPage = min($startPage + $safeWindowSize - 1, $lastPage);
    $pageUrls = $paginator->getUrlRange($startPage, $endPage);
@endphp

@if ($embedded)
    <ul class="pagination">
        @include('components.frontend.partials.pagination-items', ['paginator' => $paginator, 'pageUrls' => $pageUrls])
    </ul>
@else
    <div class="board-pagination">
        <nav aria-label="페이지 네비게이션">
            <ul class="pagination">
                @include('components.frontend.partials.pagination-items', ['paginator' => $paginator, 'pageUrls' => $pageUrls])
            </ul>
        </nav>
    </div>
@endif
