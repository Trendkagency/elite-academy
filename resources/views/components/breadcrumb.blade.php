{{-- Breadcrumb Navigation Component
     @param array $items — [['label' => 'Home', 'route' => 'home'], ['label' => 'Subjects']]
     Last item is rendered as the active breadcrumb (no link).
--}}
<nav class="flex items-center gap-2 text-xs font-mono text-slate-400 mb-8">
    @foreach ($items as $item)
        @if (! $loop->last)
            <a href="{{ route($item['route']) }}" class="hover:text-teal-600 transition-colors">{{ $item['label'] }}</a>
            <span>/</span>
        @else
            <span class="text-teal-600 font-bold">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
