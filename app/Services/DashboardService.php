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
    public function getData(
        ?string $activityEntity = null,
        ?int $activityUserId = null,
        int $activityLimit = 10,
        ?string $alertsFilter = null
    ): array {
        $partnersCount = Partner::count();
        $activePartnersCount = Partner::where('is_active', true)->count();

        $servicesCount = PartnerService::count();

        $activeObligationsCount = Obligation::query()
            ->whereNull('completed_date')
            ->count();

        $alertsList = $this->buildAlertsList($alertsFilter);
        $nextItems = $this->buildNextItemsList();

        $recentActivities = $this->buildRecentActivitiesQuery(
            activityEntity: $activityEntity,
            activityUserId: $activityUserId
        )
            ->limit($activityLimit)
            ->get();

        $groupedRecentActivities = $this->groupRecentActivities($recentActivities);

        return [
            'partnersCount' => $partnersCount,
            'activePartnersCount' => $activePartnersCount,
            'servicesCount' => $servicesCount,
            'activeObligationsCount' => $activeObligationsCount,
            'alertsCount' => $alertsList->count(),
            'alertsList' => $alertsList,
            'nextItems' => $nextItems,
            'recentActivities' => $recentActivities,
            'groupedRecentActivities' => $groupedRecentActivities,
            'activityEntity' => $activityEntity,
            'activityMine' => $activityUserId !== null,
            'activityLimit' => $activityLimit,
            'alertsFilter' => $alertsFilter,
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

    protected function groupRecentActivities(Collection $activities): Collection
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        $groups = collect([
            'Danas' => collect(),
            'Jučer' => collect(),
            'Ranije' => collect(),
        ]);

        foreach ($activities as $activity) {
            $activityDate = $activity->created_at?->copy()->startOfDay();

            if (!$activityDate) {
                $groups['Ranije']->push($activity);
                continue;
            }

            if ($activityDate->equalTo($today)) {
                $groups['Danas']->push($activity);
                continue;
            }

            if ($activityDate->equalTo($yesterday)) {
                $groups['Jučer']->push($activity);
                continue;
            }

            $groups['Ranije']->push($activity);
        }

        return $groups->filter(fn (Collection $items) => $items->isNotEmpty());
    }

    protected function buildAlertsList(?string $alertsFilter = null): Collection
    {
        $today = now()->toDateString();

        $overdueObligations = Obligation::with('partner')
            ->whereNull('completed_date')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->orderBy('due_date')
            ->get()
            ->map(fn (Obligation $obligation) => $this->mapObligationToDashboardItem($obligation));

        $todayObligations = Obligation::with('partner')
            ->whereNull('completed_date')
            ->whereNotNull('due_date')
            ->whereDate('due_date', $today)
            ->orderBy('due_date')
            ->get()
            ->map(fn (Obligation $obligation) => $this->mapObligationToDashboardItem($obligation));

        $overdueServices = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '<', $today)
            ->orderBy('expires_on')
            ->get()
            ->map(fn (PartnerService $service) => $this->mapServiceToDashboardItem($service));

        $todayServices = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', $today)
            ->orderBy('expires_on')
            ->get()
            ->map(fn (PartnerService $service) => $this->mapServiceToDashboardItem($service));

        $items = match ($alertsFilter) {
            'today' => $todayObligations->concat($todayServices),
            'overdue' => $overdueObligations->concat($overdueServices),
            default => $overdueObligations
                ->concat($overdueServices)
                ->concat($todayObligations)
                ->concat($todayServices),
        };

        return $items
            ->sortBy([
                fn ($item) => $item->status_label === 'Kasni' ? 0 : 1,
                'date',
            ])
            ->values();
    }

    protected function buildNextItemsList(int $limit = 15): Collection
    {
        $upcomingObligations = Obligation::with('partner')
            ->whereNull('completed_date')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>', now()->toDateString())
            ->orderBy('due_date')
            ->get()
            ->map(fn (Obligation $obligation) => $this->mapObligationToDashboardItem($obligation));

        $upcomingServices = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '>', now()->toDateString())
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