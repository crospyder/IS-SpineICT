<?php

namespace App\Providers;

use App\Services\TopbarNotificationService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            if (!auth()->check()) {
                return;
            }

            $view->with(app(TopbarNotificationService::class)->getData());
        });
    }
}