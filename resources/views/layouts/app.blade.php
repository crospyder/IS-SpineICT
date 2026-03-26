<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>SpineICT OPS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="flex h-screen overflow-hidden app-shell">

    @include('partials.sidebar')

    <div class="flex-1 flex flex-col">
        <header class="h-14 app-topbar flex items-center px-6">
            <div class="flex-1">
                <h1 class="text-sm app-muted">@yield('title')</h1>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleDark()" class="app-button-secondary">
                    🌓
                </button>

                <div class="text-xs app-muted">
                    SpineICT OPS v0.0.1
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 app-main">
            @yield('content')
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