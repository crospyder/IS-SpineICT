@props([
    'active' => false,
])

@php
    $baseClasses = 'flex items-center w-full px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150';

    $activeClasses = 'bg-slate-200 dark:bg-slate-800 text-slate-900 dark:text-white';

    $inactiveClasses = 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white';

    $classes = $active ? $baseClasses . ' ' . $activeClasses : $baseClasses . ' ' . $inactiveClasses;
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="truncate">
        {{ $slot }}
    </span>
</a>