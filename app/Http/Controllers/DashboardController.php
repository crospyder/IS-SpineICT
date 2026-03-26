<?php

namespace App\Http\Controllers;

use App\Models\Obligation;
use App\Models\Partner;
use App\Models\PartnerService;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'partnersCount' => Partner::count(),
            'servicesCount' => PartnerService::count(),
            'obligationsCount' => Obligation::count(),
            'expiringCount' => Obligation::expiringSoon()->count(),
            'expiringSoonList' => Obligation::with('partner')
                ->expiringSoon()
                ->orderBy('due_date')
                ->limit(5)
                ->get(),
        ]);
    }
}