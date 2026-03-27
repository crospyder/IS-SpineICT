<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Prijava | SpineICT OPS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen app-shell flex items-center justify-center px-4">
    <div class="w-full max-w-md app-card p-6 rounded-2xl shadow">
        <div class="mb-6">
            <h1 class="text-xl font-semibold">SpineICT OPS</h1>
            <div class="text-sm app-muted mt-1">Prijava u sustav</div>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm mb-2">E-mail</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full rounded-xl px-3 py-2 app-input"
                >
            </div>

            <div>
                <label for="password" class="block text-sm mb-2">Lozinka</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="w-full rounded-xl px-3 py-2 app-input"
                >
            </div>

            <label class="flex items-center gap-2 text-sm app-muted">
                <input type="checkbox" name="remember" value="1">
                Zapamti me
            </label>

            <button type="submit" class="w-full app-button">
                Prijava
            </button>
        </form>
    </div>
</body>
</html>