<?php

namespace App\Services;

use App\Models\Obligation;
use App\Models\PartnerService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TopbarNotificationService
{
    public function getData(): array
    {
        $today = now()->toDateString();

        $overdueObligations = Obligation::with('partner')
            ->whereNull('completed_date')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->orderBy('due_date')
            ->get()
            ->map(fn (Obligation $obligation) => $this->mapObligation($obligation));

        $todayObligations = Obligation::with('partner')
            ->whereNull('completed_date')
            ->whereNotNull('due_date')
            ->whereDate('due_date', $today)
            ->orderBy('due_date')
            ->get()
            ->map(fn (Obligation $obligation) => $this->mapObligation($obligation));

        $overdueServices = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '<', $today)
            ->orderBy('expires_on')
            ->get()
            ->map(fn (PartnerService $service) => $this->mapService($service));

        $todayServices = PartnerService::with('partner')
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', $today)
            ->orderBy('expires_on')
            ->get()
            ->map(fn (PartnerService $service) => $this->mapService($service));

        $overdueCount = $overdueObligations->count() + $overdueServices->count();
        $todayCount = $todayObligations->count() + $todayServices->count();

        $alertItems = $overdueObligations
            ->concat($overdueServices)
            ->concat($todayObligations)
            ->concat($todayServices)
            ->sortBy([
    fn ($item) => $item->status_label === 'Kasni' ? 0 : 1,
    'date',
])
            ->take(8)
            ->values();

        return [
            'topbarNotificationOverdueCount' => $overdueCount,
            'topbarNotificationTodayCount' => $todayCount,
            'topbarNotificationTotalCount' => $overdueCount + $todayCount,
            'topbarNotificationItems' => $alertItems,
        ];
    }

    protected function mapObligation(Obligation $obligation): object
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
        ];
    }

    protected function mapService(PartnerService $service): object
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