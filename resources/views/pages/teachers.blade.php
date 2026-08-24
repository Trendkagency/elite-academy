@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-[#FAFAF9] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Teacher Directory Header --}}
        <div class="space-y-2 border-b border-slate-200/80 pb-6">
            <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                {{ __('Meet Our') }} <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">{{ __('Expert Teachers') }}</span>
            </h1>
            <p class="text-slate-600 text-base font-medium">
                {{ __('Browse experienced teachers by subject and grade level.') }}
            </p>
        </div>

        {{-- Teacher Filters Toolbar --}}
        <form id="teachers-filter-form" method="GET" action="{{ route('teachers') }}" class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-lg space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2 space-y-1">
                    <label class="block text-xs font-mono font-extrabold text-slate-500 uppercase tracking-wider">{{ __('Search Teacher') }}</label>
                    <div class="relative">
                        <input type="text" id="teacher-search-input" name="q" value="{{ $searchQuery ?? '' }}" placeholder="{{ __('Search teacher by name, title, or specialization...') }}" class="w-full h-11 bg-[#FAFAF9] border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 focus:outline-teal-600">
                        <span id="search-spinner" class="hidden absolute right-3 top-3 text-slate-400 text-xs animate-spin">⏳</span>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-mono font-extrabold text-slate-500 uppercase tracking-wider">{{ __('Subject Filter') }}</label>
                    <select id="teacher-subject-select" name="subject" class="w-full h-11 bg-[#FAFAF9] border border-slate-200 rounded-xl px-3.5 text-sm font-semibold text-slate-800 focus:outline-teal-600 cursor-pointer">
                        <option value="">{{ __('All Subjects') }}</option>
                        @foreach ($subjects as $s)
                            <option value="{{ $s->slug }}" @selected(($selectedSubject ?? '') === $s->slug)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1 flex items-end">
                    <button type="submit" class="w-full h-11 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold font-mono transition-all card-lift">
                        {{ __('Filter Results') }}
                    </button>
                </div>
            </div>

            {{-- Subject Filter Chips --}}
            <div class="flex flex-wrap items-center gap-2 text-xs font-mono font-bold pt-4 border-t border-slate-100">
                <span class="text-slate-400 mr-2 uppercase">{{ __('Subject Filters:') }}</span>
                <button type="button" data-subject=""
                   @class([
                       'subject-chip px-3.5 py-1.5 rounded-full text-xs font-bold transition-all border cursor-pointer',
                       'bg-teal-600 text-white border-teal-600 shadow-xs' => empty($selectedSubject),
                       'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' => ! empty($selectedSubject),
                   ])>
                    {{ __('All') }}
                </button>
                @foreach ($subjects as $s)
                    @php $isActive = strtolower($selectedSubject ?? '') === strtolower($s->slug); @endphp
                    <button type="button" data-subject="{{ $s->slug }}"
                       @class([
                           'subject-chip px-3.5 py-1.5 rounded-full text-xs font-bold transition-all border cursor-pointer',
                           'bg-teal-600 text-white border-teal-600 shadow-xs' => $isActive,
                           'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' => ! $isActive,
                       ])>
                        {{ $s->name }}
                    </button>
                @endforeach
            </div>
        </form>

        {{-- Counter Info Row --}}
        <div class="flex items-center justify-between text-xs font-mono font-bold text-slate-600 px-2 py-1 bg-white rounded-2xl border border-slate-200/80 shadow-2xs">
            <span id="faculty-counter">
                {{ __('Showing') }} <strong id="count-from" class="text-teal-600">{{ $teachers->firstItem() ?? 0 }}</strong>–<strong id="count-to" class="text-teal-600">{{ $teachers->lastItem() ?? 0 }}</strong> {{ __('of') }} <strong id="count-total" class="text-slate-900">{{ number_format($teachers->total()) }}</strong> {{ __('Teachers') }}
            </span>
            <span class="hidden sm:inline text-slate-400">{{ __('Faculty Directory • Accredited Tracks') }}</span>
        </div>

        {{-- Teachers Main Content & Loading State --}}
        <div class="relative min-h-[300px]">
            {{-- Loading Spinner Overlay --}}
            <div id="teachers-loading-overlay" class="hidden absolute inset-0 bg-white/70 backdrop-blur-xs z-10 flex items-center justify-center rounded-3xl transition-opacity">
                <div class="bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-2xl font-mono text-xs font-bold flex items-center gap-3">
                    <span class="animate-spin text-teal-400">⏳</span> {{ __('Updating Teachers Directory...') }}
                </div>
            </div>

            {{-- Teachers Grid --}}
            <div id="teachers-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @include('partials.teachers-grid-items', ['teachers' => $teachers])
            </div>
        </div>

        {{-- Dynamic Pagination Container --}}
        <div id="pagination-container" class="pt-6">
            {!! $teachers->links('components.pagination') !!}
        </div>

    </div>
</section>

{{-- Real-time Dynamic AJAX JavaScript Engine --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('teachers-filter-form');
    const searchInput = document.getElementById('teacher-search-input');
    const subjectSelect = document.getElementById('teacher-subject-select');
    const subjectChips = document.querySelectorAll('.subject-chip');
    const gridContainer = document.getElementById('teachers-grid-container');
    const paginationContainer = document.getElementById('pagination-container');
    const loadingOverlay = document.getElementById('teachers-loading-overlay');
    const searchSpinner = document.getElementById('search-spinner');

    const countFrom = document.getElementById('count-from');
    const countTo = document.getElementById('count-to');
    const countTotal = document.getElementById('count-total');

    let debounceTimer = null;
    let activeSubject = '{{ $selectedSubject ?? "" }}';
    let currentPage = {{ $teachers->currentPage() }};

    function fetchTeachers(page = 1) {
        currentPage = page;
        const q = searchInput.value.trim();
        const subject = subjectSelect.value;

        // Build Query URL
        const params = new URLSearchParams();
        if (q) params.set('q', q);
        if (subject) params.set('subject', subject);
        if (page > 1) params.set('page', page);

        const requestUrl = `{{ route('teachers') }}?${params.toString()}`;

        // Show UI Loading State
        loadingOverlay.classList.remove('hidden');
        if (searchSpinner) searchSpinner.classList.remove('hidden');

        fetch(requestUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update Grid & Pagination HTML
                gridContainer.innerHTML = data.html;
                paginationContainer.innerHTML = data.pagination_html;

                // Update Counter Stats
                if (countFrom) countFrom.textContent = data.from;
                if (countTo) countTo.textContent = data.to;
                if (countTotal) countTotal.textContent = data.total.toLocaleString();

                // Update Chip Highlight State
                updateSubjectChips(subject);

                // Push URL State
                history.pushState(null, '', requestUrl);
            }
        })
        .catch(error => {
            console.error('AJAX Teacher Fetch Error:', error);
        })
        .finally(() => {
            loadingOverlay.classList.add('hidden');
            if (searchSpinner) searchSpinner.classList.add('hidden');
        });
    }

    function updateSubjectChips(selectedSubject) {
        subjectChips.forEach(chip => {
            const chipSubject = chip.getAttribute('data-subject');
            const isActive = (chipSubject === selectedSubject) || (chipSubject === '' && !selectedSubject);

            if (isActive) {
                chip.className = 'subject-chip px-3.5 py-1.5 rounded-full text-xs font-bold transition-all border cursor-pointer bg-teal-600 text-white border-teal-600 shadow-xs';
            } else {
                chip.className = 'subject-chip px-3.5 py-1.5 rounded-full text-xs font-bold transition-all border cursor-pointer bg-white text-slate-700 hover:bg-slate-100 border-slate-200';
            }
        });
    }

    // Event 1: Form Submit Guard
    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchTeachers(1);
    });

    // Event 2: Debounced Real-time Search Typing (300ms)
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchTeachers(1);
        }, 300);
    });

    // Event 3: Subject Dropdown Change
    subjectSelect.addEventListener('change', function () {
        fetchTeachers(1);
    });

    // Event 4: Subject Filter Chips Click
    subjectChips.forEach(chip => {
        chip.addEventListener('click', function () {
            const selectedSubject = this.getAttribute('data-subject');
            subjectSelect.value = selectedSubject;
            fetchTeachers(1);
        });
    });

    // Event 5: Dynamic Pagination Page Links Click Delegation
    paginationContainer.addEventListener('click', function (e) {
        const link = e.target.closest('.pagination-link');
        if (link) {
            e.preventDefault();
            const targetPage = link.getAttribute('data-page');
            if (targetPage) {
                fetchTeachers(parseInt(targetPage, 10));
                window.scrollTo({ top: gridContainer.offsetTop - 120, behavior: 'smooth' });
            }
        }
    });

    // Handle Browser Back/Forward Buttons
    window.addEventListener('popstate', function () {
        const urlParams = new URLSearchParams(window.location.search);
        searchInput.value = urlParams.get('q') || '';
        subjectSelect.value = urlParams.get('subject') || '';
        const page = parseInt(urlParams.get('page') || '1', 10);
        fetchTeachers(page);
    });
});
</script>
@endsection
