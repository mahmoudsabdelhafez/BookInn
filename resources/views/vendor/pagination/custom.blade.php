@if ($paginator->hasPages())  {{-- Check if there are multiple pages --}}
    
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())  {{-- Check if we are on the first page --}}
        <a class="prev page-numbers disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
            <span aria-hidden="true"> <i class='bx bx-chevrons-left'></i></span> {{-- Display previous icon for disabled state --}}
        </a>
    @else
        <a class="prev page-numbers" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
            <i class='bx bx-chevrons-left'></i> {{-- Display previous icon as a clickable link --}}
        </a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)  {{-- Loop through each pagination element --}}
        
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))  {{-- If the element is a string (i.e., "three dots" separator) --}}
            <span class="page-numbers current" aria-current="page">{{ $element }}</span>  {{-- Display "..." as a non-clickable item --}}
        @endif

        {{-- Array of Page Links --}}
        @if (is_array($element))  {{-- If the element is an array (i.e., page numbers) --}}
            @foreach ($element as $page => $url)  {{-- Loop through each page number --}}
                @if ($page == $paginator->currentPage())  {{-- Check if the page is the current page --}}
                    <span class="page-numbers current" aria-current="page">{{ $page }}</span>  {{-- Highlight the current page --}}
                @else
                    <a class="page-numbers" href="{{ $url }}">{{ $page }}</a>  {{-- Display a clickable page number link --}}
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())  {{-- Check if there are more pages available --}}
        <a class="page-numbers" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
            <i class='bx bx-chevrons-right'></i> {{-- Display next icon as a clickable link --}}
        </a>
    @else
        <a class="next page-numbers disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
            <span aria-hidden="true"> <i class='bx bx-chevrons-right'></i></span> {{-- Display next icon for disabled state --}}
        </a>
    @endif

@endif  {{-- End of pagination check --}}


{{-- <a href="#" class="prev page-numbers">
    <i class='bx bx-chevrons-left'></i>
</a> --}}

{{-- <span class="page-numbers current" aria-current="page">1</span>
<a href="#" class="page-numbers">2</a>
<a href="#" class="page-numbers">3</a> --}}

{{-- <a href="#" class="next page-numbers">
    <i class='bx bx-chevrons-right'></i>
</a> --}}