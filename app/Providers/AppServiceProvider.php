<?php

namespace App\Providers;

use App\Models\Contract;
use App\Models\Quotation;
use App\Observers\ContractObserver;
use App\Observers\QuotationObserver;
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
        Quotation::observe(QuotationObserver::class);
        Contract::observe(ContractObserver::class);
    }
}
