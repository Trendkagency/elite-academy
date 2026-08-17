{{-- Filter Chip Component
     @param string $label — Chip text
     @param bool $active — Whether chip is currently active (default false)
--}}
@php $active = $active ?? false; @endphp

<button @class([
    'px-4 py-2 rounded-xl text-xs font-semibold transition-colors cursor-pointer',
    'bg-teal-600 text-white shadow-xs' => $active,
    'bg-white text-slate-700 border border-slate-200 hover:border-teal-500' => ! $active,
])>{{ $label }}</button>
