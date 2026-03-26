<aside class="w-64 app-sidebar flex flex-col">

    <div class="h-14 flex items-center px-6 border-b border-slate-200 dark:border-slate-800">
        <span class="font-semibold text-sm tracking-wide">
            SpineICT OPS
        </span>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
            Dashboard
        </x-nav-link>

        <x-nav-link href="{{ route('partners.index') }}" :active="request()->routeIs('partners.*')">
            Partneri
        </x-nav-link>

        <x-nav-link href="{{ route('partner-services.index') }}" :active="request()->routeIs('partner-services.*')">
            Usluge
        </x-nav-link>

        <x-nav-link href="{{ route('obligations.index') }}" :active="request()->routeIs('obligations.*')">
            Obveze
        </x-nav-link>
    </nav>

</aside>