
@if ($paginator->hasPages())
    <nav aria-label="Page navigation example nav-partent">
        <ul class="pagination justify-content-center nav-partent">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled nav-partent border-0 disabled">
                    <a class="page-link nav-partent border-0" href="#"
                       tabindex="-1"> |< </a>
                </li>
            @else
                <li class="page-item nav-partent border-0">
                    <a class="page-link nav-partent border-0" href="{{ $paginator->previousPageUrl() }}">
                        |< </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled">{{ $element }}</li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <a class="page-link border-0 nav-partent-active">{{ $page }}</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link border-0 nav-partent"
                                   href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link nav-partent border-0"
                       href="{{ $paginator->nextPageUrl() }}"
                       rel="next"> >| </a>
                </li>
            @else
                <li class="page-item disabled">
                    <a class="page-link nav-partent border-0" href="#"> >| </a>
                </li>
            @endif
        </ul>
    </nav>
@endif

