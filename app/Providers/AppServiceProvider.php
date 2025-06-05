<?php

namespace App\Providers;

use Livewire\Livewire;
use Illuminate\Pagination\Paginator;
use App\Livewire\Orders\OrderDelivery;
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
        //
         Paginator::useBootstrapFive();

    }
}
