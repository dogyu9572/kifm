@props(['paginator', 'embedded' => false])

@if ($embedded)
    <ul class="pagination">
        @include('components.frontend.partials.pagination-items', ['paginator' => $paginator])
    </ul>
@else
    <div class="board-pagination">
        <nav aria-label="페이지 네비게이션">
            <ul class="pagination">
                @include('components.frontend.partials.pagination-items', ['paginator' => $paginator])
            </ul>
        </nav>
    </div>
@endif

