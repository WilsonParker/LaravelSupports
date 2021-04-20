@if ($paginator->hasPages())
    @php($pageLength = sizeof($elements[2] ?? $elements[0]))
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;&lsaquo;</span>
                </li>
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link"
                       href="#"
                       rel="prev" aria-label="@lang('pagination.previous')"
                       onclick="goToPage({{ $paginator->currentPage() - $pageLength > 0 ? $paginator->currentPage() - $pageLength : 1 }})"
                    >&lsaquo;&lsaquo;</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                       aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                       aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#" rel="next"
                       aria-label="@lang('pagination.next')"
                       onclick="goToPage({{ $paginator->currentPage() + $pageLength > $paginator->lastPage() ? $paginator->lastPage() : $paginator->currentPage() + $pageLength }})"
                    >&rsaquo;&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;&rsaquo;</span>
                </li>
            @endif
        </ul>
        <div class="input-group">
            <input type="number" class="form-control" id="input-go-to-page" aria-describedby="inputGoToPageAddon"
                   aria-label="Go to page">
            <button class="btn btn-outline-secondary" type="button" id="inputGoToPageAddon" onclick="goToPageClick()">Go
            </button>
        </div>
    </nav>
@endif
<script>
    function goToPage(page) {
        let query = helper.html.addQueryString('page', page);
        location.href = "?" + query;
    }

    function goToPageClick() {
        let page = $('#input-go-to-page').val();
        goToPage(page);
    }
</script>
