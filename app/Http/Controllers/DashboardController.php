<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
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

        $expiringServicesCount = PartnerService::query()
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '<=', now()->addDays(30))
            ->count();

        $overdueObligationsCount = Obligation::overdue()->count();
        $todayObligationsCount = Obligation::query()
            ->whereNull('completed_date')
            ->whereDate('due_date', now()->toDateString())
            ->count();

        $expiringObligationsCount = Obligation::expiringSoon()->count();

        $overdueServicesCount = PartnerService::query()
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '<', now()->toDateString())
            ->count();

        $todayServicesCount = PartnerService::query()
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', now()->toDateString())
            ->count();

        $upcomingServicesCount = PartnerService::query()
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '>', now()->toDateString())
            ->whereDate('expires_on', '<=', now()->addDays(30))
            ->count();

        $obligationsCount = Obligation::count();
        $overdueCount = $overdueObligationsCount + $overdueServicesCount;
        $todayCount = $todayObligationsCount + $todayServicesCount;
        $expiringCount = $expiringObligationsCount + $upcomingServicesCount;

        $overdueObligations = Obligation::with('partner')
            ->overdue()
            ->orderBy('due_date')
            ->get()
            ->map(function ($obligation) {
                return (object) [
                    'kind' => 'obligation',
                    'partner_name' => $obligation->partner->name ?? '-',
                    'title' => $obligation->title,
                    'date' => $obligation->due_date,
                    'url' => route('obligations.edit', $obligation),
                    'status_label' => 'Kasni',
                ];
            });

        $overdueServices = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '<', now()->toDateString())
            ->orderBy('expires_on')
            ->get()
            ->map(function ($service) {
                return (object) [
                    'kind' => 'service',
                    'partner_name' => $service->partner->name ?? '-',
                    'title' => $service->name,
                    'date' => $service->expires_on,
                    'url' => route('partner-services.edit', $service),
                    'status_label' => 'Istekla usluga',
                ];
            });

        $overdueList = $overdueObligations
            ->concat($overdueServices)
            ->sortBy('date')
            ->take(5)
            ->values();

        $todayObligations = Obligation::with('partner')
            ->whereNull('completed_date')
            ->whereDate('due_date', now()->toDateString())
            ->orderBy('due_date')
            ->get()
            ->map(function ($obligation) {
                return (object) [
                    'kind' => 'obligation',
                    'partner_name' => $obligation->partner->name ?? '-',
                    'title' => $obligation->title,
                    'date' => $obligation->due_date,
                    'url' => route('obligations.edit', $obligation),
                ];
            });

        $todayServices = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', now()->toDateString())
            ->orderBy('expires_on')
            ->get()
            ->map(function ($service) {
                return (object) [
                    'kind' => 'service',
                    'partner_name' => $service->partner->name ?? '-',
                    'title' => $service->name,
                    'date' => $service->expires_on,
                    'url' => route('partner-services.edit', $service),
                ];
            });

        $todayList = $todayObligations
            ->concat($todayServices)
            ->sortBy('date')
            ->take(5)
            ->values();

        $expiringSoonList = Obligation::with('partner')
            ->expiringSoon()
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $expiringServicesList = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '<=', now()->addDays(30))
            ->orderBy('expires_on')
            ->limit(5)
            ->get();

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'partnersCount',
            'activePartnersCount',
            'servicesCount',
            'expiringServicesCount',
            'obligationsCount',
            'overdueCount',
            'todayCount',
            'expiringCount',
            'overdueList',
            'todayList',
            'expiringSoonList',
            'expiringServicesList',
            'recentActivities'
        ));
    }
}