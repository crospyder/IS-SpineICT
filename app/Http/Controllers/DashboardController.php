<?php

namespace App\Http\Controllers;

use App\Models\Obligation;
use App\Models\Partner;
use App\Models\PartnerService;

class DashboardController extends Controller
{
    public function index()
    {
        $partnersCount = Partner::count();
        $activePartnersCount = Partner::where('is_active', true)->count();

        $servicesCount = PartnerService::count();
        $expiringServicesCount = PartnerService::whereNotNull('expires_on')
            ->whereDate('expires_on', '<=', now()->addDays(30))
            ->whereDate('expires_on', '>=', now())
            ->count();

        $obligationsCount = Obligation::count();
        $overdueCount = Obligation::overdue()->count();
        $expiringCount = Obligation::expiringSoon()->count();

        $expiringSoonList = Obligation::with('partner')
            ->expiringSoon()
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $overdueList = Obligation::with('partner')
            ->overdue()
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $expiringServicesList = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '<=', now()->addDays(30))
            ->whereDate('expires_on', '>=', now())
            ->orderBy('expires_on')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'partnersCount',
            'activePartnersCount',
            'servicesCount',
            'expiringServicesCount',
            'obligationsCount',
            'overdueCount',
            'expiringCount',
            'expiringSoonList',
            'overdueList',
            'expiringServicesList'
        ));
    }
}