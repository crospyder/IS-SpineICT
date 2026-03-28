<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {
    }

    public function index(Request $request)
    {
        $activityEntity = $request->string('activity_entity')->toString() ?: null;
        $activityMine = $request->boolean('activity_mine');
        $activityLimit = (int) $request->integer('activity_limit', 10);
        $alertsFilter = $request->string('alerts')->toString() ?: null;

        if (!in_array($activityLimit, [10, 25, 50], true)) {
            $activityLimit = 10;
        }

        if (!in_array($alertsFilter, [null, 'all', 'overdue', 'today'], true)) {
            $alertsFilter = null;
        }

        return view('dashboard', $this->dashboardService->getData(
            activityEntity: $activityEntity,
            activityUserId: $activityMine ? auth()->id() : null,
            activityLimit: $activityLimit,
            alertsFilter: $alertsFilter,
        ));
    }
}