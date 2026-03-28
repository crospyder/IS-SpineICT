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
                <a href="{{ route('dashboard') }}" class="app-button-secondary flex items-center gap-2">
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
                </a>

                @if(($topbarNotificationOverdueCount ?? 0) > 0 || ($topbarNotificationTodayCount ?? 0) > 0)
                    <div class="hidden 2xl:flex items-center gap-2 text-xs">
                        @if(($topbarNotificationOverdueCount ?? 0) > 0)
                            <span class="app-badge badge-overdue">
                                Kasni: {{ $topbarNotificationOverdueCount }}
                            </span>
                        @endif

                        @if(($topbarNotificationTodayCount ?? 0) > 0)
                            <span class="app-badge badge-soon">
                                Danas: {{ $topbarNotificationTodayCount }}
                            </span>
                        @endif
                    </div>
                @endif

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
                    SpineICT OPS v0.1.1
                </div>
            </div>
        </header>

        <main class="flex-1 min-w-0 overflow-y-auto app-main">
            <div class="w-full max-w-none p-6">
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
</body>
</html>