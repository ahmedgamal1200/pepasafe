@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center items-center space-x-1 rtl:space-x-reverse">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 text-sm text-white bg-blue-300 rounded cursor-not-allowed">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-blue-300 rounded hover:bg-blue-50">‹</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-2 text-sm text-gray-500">...</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-2 text-sm text-white bg-blue-600 border border-blue-600 rounded">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-blue-300 rounded hover:bg-blue-50">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-blue-300 rounded hover:bg-blue-50">›</a>
        @else
            <span class="px-3 py-2 text-sm text-white bg-blue-300 rounded cursor-not-allowed">›</span>
        @endif
    </nav>
@endif
