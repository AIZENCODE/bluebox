<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Client;
use App\Models\Companie;
use App\Models\Contract;
use App\Models\Data;
use App\Models\Post;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Tag;
use App\Observers\AccountObserver;
use App\Observers\CategoryObserver;
use App\Observers\ClientObserver;
use App\Observers\CompanieObserver;
use App\Observers\ContractObserver;
use App\Observers\DataObserver;
use App\Observers\PostObserver;
use App\Observers\ProductObserver;
use App\Observers\QuotationObserver;
use App\Observers\TagObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\LayoutClient;

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
        Post::observe(PostObserver::class);
        Data::observe(DataObserver::class);
        Account::observe(AccountObserver::class);
        Companie::observe(CompanieObserver::class);
        Product::observe(ProductObserver::class);
        Client::observe(ClientObserver::class);
        Category::observe(CategoryObserver::class);
        Tag::observe(TagObserver::class);

    }
}
