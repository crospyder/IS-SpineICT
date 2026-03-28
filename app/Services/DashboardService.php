<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Obligation;
use App\Models\Partner;
use App\Models\PartnerService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    public function getData(?string $activityEntity = null, ?int $activityUserId = null, int $activityLimit = 10): array
    {
        $partnersCount = Partner::count();
        $activePartnersCount = Partner::where('is_active', true)->count();

        $servicesCount = PartnerService::count();

        $activeObligationsCount = Obligation::query()
            ->whereNull('completed_date')
            ->count();

        $alertsList = $this->buildAlertsList();
        $nextItems = $this->buildNextItemsList();

        $recentActivities = $this->buildRecentActivitiesQuery(
            activityEntity: $activityEntity,
            activityUserId: $activityUserId
        )
            ->limit($activityLimit)
            ->get();

        return [
            'partnersCount' => $partnersCount,
            'activePartnersCount' => $activePartnersCount,
            'servicesCount' => $servicesCount,
            'activeObligationsCount' => $activeObligationsCount,
            'alertsCount' => $alertsList->count(),
            'alertsList' => $alertsList,
            'nextItems' => $nextItems,
            'recentActivities' => $recentActivities,
            'activityEntity' => $activityEntity,
            'activityMine' => $activityUserId !== null,
            'activityLimit' => $activityLimit,
            'activityAvailableEntities' => [
                'obligation' => 'Obveze',
                'service' => 'Usluge',
                'partner' => 'Partneri',
                'procurement' => 'Kalkulacije',
            ],
        ];
    }

    protected function buildRecentActivitiesQuery(?string $activityEntity = null, ?int $activityUserId = null)
    {
        $query = ActivityLog::with('user')->latest();

        if ($activityEntity) {
            $query->where('entity_type', $activityEntity);
        }

        if ($activityUserId) {
            $query->where('user_id', $activityUserId);
        }

        return $query;
    }

    protected function buildAlertsList(): Collection
    {
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

        return $overdueObligations
            ->concat($overdueServices)
            ->sortBy('date')
            ->values();
    }

    protected function buildNextItemsList(int $limit = 15): Collection
    {
        $upcomingObligations = Obligation::with('partner')
            ->whereNull('completed_date')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', now()->toDateString())
            ->orderBy('due_date')
            ->get()
            ->map(fn (Obligation $obligation) => $this->mapObligationToDashboardItem($obligation));

        $upcomingServices = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '>=', now()->toDateString())
            ->orderBy('expires_on')
            ->get()
            ->map(fn (PartnerService $service) => $this->mapServiceToDashboardItem($service));

        return $upcomingObligations
            ->concat($upcomingServices)
            ->sortBy('date')
            ->take($limit)
            ->values();
    }

    protected function mapObligationToDashboardItem(Obligation $obligation): object
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

    protected function mapServiceToDashboardItem(PartnerService $service): object
    {
        $date = $service->expires_on ? Carbon::parse($service->expires_on) : null;
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

    protected function resolveStatusLabel(?int $daysDiff): string
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

    protected function resolveStatusClass(?int $daysDiff): string
    {
        if ($daysDiff === null) {
            return 'badge-soon';
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