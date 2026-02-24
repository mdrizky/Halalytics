<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Services\FirebaseService::class, function ($app) {
            return new \App\Services\FirebaseService();
        });

        $this->app->singleton(\App\Services\OpenFoodFactsService::class, function ($app) {
            return new \App\Services\OpenFoodFactsService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // View Composer for Admin Global Stats
        view()->composer('admin.*', function ($view) {
            $view->with('global_user_count', \App\Models\User::count());
            $view->with('global_product_count', \App\Models\ProductModel::count());
            $view->with('global_category_count', \App\Models\KategoriModel::count());
            $view->with('global_banner_count', \App\Models\Banner::count());
            $view->with('global_scan_count', \App\Models\ScanModel::count());
            $view->with('global_report_count', \App\Models\ReportModel::where('status', 'pending')->count());
            $view->with('global_street_food_count', \App\Models\StreetFood::count());
        });
    }
}
