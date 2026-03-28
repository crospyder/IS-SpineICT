<?php

namespace App\Services;

use App\Models\Obligation;
use App\Models\PartnerService;

class TopbarNotificationService
{
    public function getData(): array
    {
        $today = now()->toDateString();

        $overdueObligationsCount = Obligation::query()
            ->whereNull('completed_date')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->count();

        $todayObligationsCount = Obligation::query()
            ->whereNull('completed_date')
            ->whereDate('due_date', $today)
            ->count();

        $overdueServicesCount = PartnerService::query()
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', '<', $today)
            ->count();

        $todayServicesCount = PartnerService::query()
            ->whereNotNull('expires_on')
            ->whereDate('expires_on', $today)
            ->count();

        $overdueCount = $overdueObligationsCount + $overdueServicesCount;
        $todayCount = $todayObligationsCount + $todayServicesCount;

        return [
            'topbarNotificationOverdueCount' => $overdueCount,
            'topbarNotificationTodayCount' => $todayCount,
            'topbarNotificationTotalCount' => $overdueCount + $todayCount,
        ];
    }
}