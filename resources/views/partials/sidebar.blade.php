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

        <x-nav-link href="{{ route('procurements.index') }}" :active="request()->routeIs('procurements.*')">
            Nabava / Kalkulacije
        </x-nav-link>

        @if(auth()->user()?->is_admin)
            <div class="pt-4 mt-4 border-t border-white/10">
                <div class="px-3 pb-2 text-[11px] uppercase tracking-wider app-muted">
                    Administracija
                </div>

                <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')">
                    Korisnici
                </x-nav-link>
            </div>
        @endif
    </nav>

</aside>