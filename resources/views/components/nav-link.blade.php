@props(['active'])

<a {{ $attributes->merge([
    'class' => 'block px-4 py-2 rounded-md transition ' . (
        $active
            ? 'bg-slate-200 dark:bg-slate-800 text-slate-900 dark:text-white'
            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white'
    )
]) }}>
    {{ $slot }}
</a>