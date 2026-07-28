@if ($paginator->hasPages())
    <div class="blog-pagination-inner">
        @if ($paginator->onFirstPage())
            <span class="blog-page-btn is-disabled" aria-disabled="true">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="blog-page-btn" rel="prev">Previous</a>
        @endif

        <ul class="blog-page-numbers" role="list">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="blog-page-ellipsis" aria-hidden="true">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="blog-page-num is-current" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="blog-page-num">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="blog-page-btn" rel="next">Next</a>
        @else
            <span class="blog-page-btn is-disabled" aria-disabled="true">Next</span>
        @endif
    </div>
@endif
