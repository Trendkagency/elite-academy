@if ($paginator->hasPages())
    @php
        $locale = app()->getLocale();
        $isRtl = $locale === 'ar';
        $elements = $elements ?? [];
    @endphp
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between font-mono text-xs font-bold text-slate-700">
        {{-- Previous Page Link --}}
        <div>
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-xl cursor-not-allowed border border-slate-200 opacity-60">
                    {!! $isRtl ? 'التالي &rarr;' : '&larr; Previous' !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" data-page="{{ $paginator->currentPage() - 1 }}" class="pagination-link px-4 py-2 bg-white hover:bg-slate-50 text-slate-800 rounded-xl border border-slate-200/90 shadow-2xs transition-all card-lift flex items-center gap-1.5">
                    {!! $isRtl ? 'التالي &rarr;' : '&larr; Previous' !!}
                </a>
            @endif
        </div>

        {{-- Page Numbers --}}
        <div class="hidden sm:flex items-center gap-1.5">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-2 text-slate-400 font-extrabold">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3.5 py-2 bg-teal-600 text-white rounded-xl font-extrabold border border-teal-600 shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" data-page="{{ $page }}" class="pagination-link px-3.5 py-2 bg-white hover:bg-slate-100 text-slate-700 rounded-xl border border-slate-200/90 transition-all font-bold">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        <div>
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" data-page="{{ $paginator->currentPage() + 1 }}" class="pagination-link px-4 py-2 bg-white hover:bg-slate-50 text-slate-800 rounded-xl border border-slate-200/90 shadow-2xs transition-all card-lift flex items-center gap-1.5">
                    {!! $isRtl ? '&larr; السابق' : 'Next &rarr;' !!}
                </a>
            @else
                <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-xl cursor-not-allowed border border-slate-200 opacity-60">
                    {!! $isRtl ? '&larr; السابق' : 'Next &rarr;' !!}
                </span>
            @endif
        </div>
    </nav>
@endif
