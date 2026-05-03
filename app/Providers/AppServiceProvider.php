<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\InscriptionObserver;
use App\Models\Event;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(\App\Services\WaitlistService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
         Event::observe(InscriptionObserver::class);
    }
}
