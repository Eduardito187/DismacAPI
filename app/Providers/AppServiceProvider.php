<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Catalog;
use App\Models\Category;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Sales;
use Illuminate\Support\ServiceProvider;
use App\Plugins\PluginOrder;
use App\Plugins\PluginProduct;
use App\Plugins\PluginCatalog;
use App\Plugins\PluginCategory;
use App\Plugins\PluginPartner;
use App\Plugins\PluginAccount;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        Sales::observe(PluginOrder::class);
        Product::observe(PluginProduct::class);
        Category::observe(PluginCategory::class);
        Catalog::observe(PluginCatalog::class);
        Partner::observe(PluginPartner::class);
        Account::observe(PluginAccount::class);
    }
}
