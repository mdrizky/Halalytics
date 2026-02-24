@if ($paginator->hasPages())
    <div class="flex items-center gap-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <button disabled class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-300 cursor-not-allowed">
                <span class="material-icons-round text-sm">chevron_left</span>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-400 hover:bg-white hover:text-slate-600 transition-colors">
                <span class="material-icons-round text-sm">chevron_left</span>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-2 text-slate-400 text-xs">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-8 h-8 rounded-lg bg-primary text-white text-xs font-extrabold flex items-center justify-center shadow-sm shadow-primary/20">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-lg text-slate-600 dark:text-slate-400 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center justify-center">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-400 hover:bg-white hover:text-slate-600 transition-colors">
                <span class="material-icons-round text-sm">chevron_right</span>
            </a>
        @else
            <button disabled class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-300 cursor-not-allowed">
                <span class="material-icons-round text-sm">chevron_right</span>
            </button>
        @endif
    </div>
@endif
