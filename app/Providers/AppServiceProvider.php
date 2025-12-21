<?php

namespace App\Providers;

use App\Models\Entity;
use App\Observers\EntityObserver;
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
        Entity::observe(EntityObserver::class);
    }
}
