<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Obligation;
use App\Models\Partner;
use App\Models\PartnerService;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $partnersCount = Partner::count();
        $activePartnersCount = Partner::where('is_active', true)->count();

        $servicesCount = PartnerService::count();
        $activeObligationsCount = Obligation::query()
            ->whereNull('completed_date')
            ->count();

        $overdueObligations = Obligation::with('partner')
            ->overdue()
            ->orderBy('due_date')
            ->get()
            ->map(fn (Obligation $obligation) => $this->mapObligationToDashboardItem($obligation));

        $overdueServices = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '<', now()->toDateString())
            ->orderBy('expires_on')
            ->get()
            ->map(fn (PartnerService $service) => $this->mapServiceToDashboardItem($service));

        $alertsList = $overdueObligations
            ->concat($overdueServices)
            ->sortBy('date')
            ->values();

        $alertsCount = $alertsList->count();

        $upcomingObligations = Obligation::with('partner')
            ->whereNull('completed_date')
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->get()
            ->map(fn (Obligation $obligation) => $this->mapObligationToDashboardItem($obligation));

        $upcomingServices = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '>=', now()->toDateString())
            ->orderBy('expires_on')
            ->get()
            ->map(fn (PartnerService $service) => $this->mapServiceToDashboardItem($service));

        $nextItems = $upcomingObligations
            ->concat($upcomingServices)
            ->sortBy('date')
            ->take(15)
            ->values();

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'partnersCount',
            'activePartnersCount',
            'servicesCount',
            'activeObligationsCount',
            'alertsCount',
            'alertsList',
            'nextItems',
            'recentActivities'
        ));
    }

    private function mapObligationToDashboardItem(Obligation $obligation): object
    {
        $date = $obligation->due_date;
        $daysDiff = $date ? now()->startOfDay()->diffInDays($date->copy()->startOfDay(), false) : null;

        return (object) [
            'kind' => 'obligation',
            'kind_label' => 'Obveza',
            'partner_name' => $obligation->partner->name ?? '-',
            'title' => $obligation->title,
            'date' => $date,
            'url' => route('obligations.edit', $obligation),
            'status_label' => $this->resolveStatusLabel($daysDiff),
            'status_class' => $this->resolveStatusClass($daysDiff),
            'days_diff' => $daysDiff,
        ];
    }

    private function mapServiceToDashboardItem(PartnerService $service): object
    {
        $date = $service->expires_on ? \Carbon\Carbon::parse($service->expires_on) : null;
        $daysDiff = $date ? now()->startOfDay()->diffInDays($date->copy()->startOfDay(), false) : null;

        return (object) [
            'kind' => 'service',
            'kind_label' => 'Usluga',
            'partner_name' => $service->partner->name ?? '-',
            'title' => $service->name,
            'date' => $date,
            'url' => route('partner-services.edit', $service),
            'status_label' => $this->resolveStatusLabel($daysDiff),
            'status_class' => $this->resolveStatusClass($daysDiff),
            'days_diff' => $daysDiff,
        ];
    }

    private function resolveStatusLabel(?int $daysDiff): string
    {
        if ($daysDiff === null) {
            return '-';
        }

        if ($daysDiff < 0) {
            return 'Kasni';
        }

        if ($daysDiff === 0) {
            return 'Danas';
        }

        if ($daysDiff === 1) {
            return 'Za 1 dan';
        }

        return "Za {$daysDiff} dana";
    }

    private function resolveStatusClass(?int $daysDiff): string
    {
        if ($daysDiff === null) {
            return 'badge-neutral';
        }

        if ($daysDiff < 0) {
            return 'badge-overdue';
        }

        if ($daysDiff === 0) {
            return 'badge-soon';
        }

        return 'badge-ok';
    }
}