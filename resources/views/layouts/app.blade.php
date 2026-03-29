<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>SpineICT OPS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="flex min-h-screen app-shell">

    @include('partials.sidebar')

    <div class="flex-1 min-w-0 flex flex-col">
        <header class="h-14 app-topbar flex items-center justify-between px-6 border-b border-white/10">
            <div class="min-w-0">
                <h1 class="text-sm app-muted truncate">@yield('title')</h1>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <details class="relative">
                    <summary class="list-none cursor-pointer app-button-secondary flex items-center gap-2">
                        <span>Upozorenja</span>

                        @if(($topbarNotificationTotalCount ?? 0) > 0)
                            <span class="app-badge badge-overdue">
                                {{ $topbarNotificationTotalCount }}
                            </span>
                        @else
                            <span class="app-badge badge-ok">
                                0
                            </span>
                        @endif

                        <span class="text-xs app-muted">▾</span>
                    </summary>

                    <div class="absolute right-0 top-full mt-2 w-[520px] max-w-[92vw] overflow-x-hidden app-card p-4 shadow-2xl z-50">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <div class="text-sm font-semibold">Aktivna upozorenja</div>
                                <div class="text-xs app-muted">
                                    Kasni: {{ $topbarNotificationOverdueCount ?? 0 }} · Danas: {{ $topbarNotificationTodayCount ?? 0 }}
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-1 text-xs">
                                <a href="{{ route('dashboard', ['alerts' => 'all']) }}" class="app-link">
                                    Sve
                                </a>
                                <a href="{{ route('dashboard', ['alerts' => 'overdue']) }}" class="app-link">
                                    Samo kasni
                                </a>
                                <a href="{{ route('dashboard', ['alerts' => 'today']) }}" class="app-link">
                                    Samo danas
                                </a>
                            </div>
                        </div>

                        @if(($topbarNotificationItems ?? collect())->count())
                            <div class="flex flex-col gap-3 max-h-96 overflow-y-auto overflow-x-hidden pr-1">
                                @foreach($topbarNotificationItems as $item)
                                    <a href="{{ $item->url }}" class="block w-full min-w-0 border-b border-white/10 pb-3 last:border-b-0 last:pb-0 hover:bg-white/5 rounded-md px-2 -mx-2 transition">
                                        <div class="min-w-0">
                                            <div class="text-xs app-muted break-words">
                                                {{ $item->kind_label }} · {{ $item->partner_name }}
                                            </div>

                                            <div class="text-sm font-medium break-words mt-1 leading-tight">
                                                {{ $item->title }}
                                            </div>

                                            <div class="flex items-center justify-between gap-3 mt-2 min-w-0">
                                                <div class="text-xs app-muted">
                                                    {{ $item->date ? $item->date->format('d.m.Y') : '-' }}
                                                </div>

                                                <div class="shrink-0">
                                                    <span class="app-badge {{ $item->status_class }}">
                                                        {{ $item->status_label }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-sm app-muted">
                                Nema aktivnih upozorenja.
                            </div>
                        @endif
                    </div>
                </details>

                <button type="button" onclick="toggleDark()" class="app-button-secondary">
                    🌓
                </button>

                <div class="text-xs app-muted hidden lg:block">
                    {{ auth()->user()->name }}
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="app-button-secondary">
                        Odjava
                    </button>
                </form>

                <div class="text-xs app-muted hidden xl:block">
                    SpineICT OPS v0.3.1
                </div>
            </div>
        </header>

        <main class="flex-1 min-w-0 overflow-y-auto app-main">
            <div class="w-full max-w-none p-6">

                @if(session('success'))
                    <div class="app-card p-4 mb-6 border border-emerald-500/30 bg-emerald-500/10">
                        <div class="text-sm font-medium text-emerald-300">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('status'))
                    <div class="app-card p-4 mb-6 border border-emerald-500/30 bg-emerald-500/10">
                        <div class="text-sm font-medium text-emerald-300">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="app-card p-4 mb-6 border border-red-500/30 bg-red-500/10">
                        <div class="text-sm font-medium text-red-300 mb-2">
                            Provjeri unesene podatke.
                        </div>

                        <ul class="text-sm space-y-1 text-red-200">
                            @foreach($errors->all() as $error)
                                <li>— {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</div>

<script>
function setTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    localStorage.setItem('theme', theme);
}

function toggleDark() {
    const isDark = document.documentElement.classList.contains('dark');
    setTheme(isDark ? 'light' : 'dark');
}

(function () {
    const saved = localStorage.getItem('theme');

    if (saved === 'dark' || saved === 'light') {
        setTheme(saved);
        return;
    }

    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    setTheme(prefersDark ? 'dark' : 'light');
})();
</script>

<script>
document.addEventListener('click', function (e) {
    document.querySelectorAll('details').forEach((dropdown) => {
        if (!dropdown.contains(e.target)) {
            dropdown.removeAttribute('open');
        }
    });
});
</script>
</body>
</html>