<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrap();
        // View Composer for Admin Global Stats
        view()->composer('admin.*', function ($view) {
            $safeCount = function (string $table, callable $resolver): int {
                if (!Schema::hasTable($table)) {
                    return 0;
                }

                try {
                    return (int) $resolver();
                } catch (\Throwable $e) {
                    return 0;
                }
            };

            $view->with('global_user_count', $safeCount('users', fn () => \App\Models\User::count()));
            $view->with('global_product_count', $safeCount('products', fn () => \App\Models\ProductModel::count()));
            $view->with('global_category_count', $safeCount('kategori', fn () => \App\Models\KategoriModel::count()));
            $view->with('global_banner_count', $safeCount('banners', fn () => \App\Models\Banner::count()));
            $view->with('global_scan_count', $safeCount('scans', fn () => \App\Models\ScanModel::count()));
            $view->with('global_report_count', $safeCount('reports', fn () => \App\Models\ReportModel::where('status', 'pending')->count()));
            $view->with('global_street_food_count', $safeCount('street_foods', fn () => \App\Models\StreetFood::count()));
            $view->with('global_medicine_count', $safeCount('medicines', fn () => \App\Models\Medicine::count()));
            $view->with('global_cosmetic_count', $safeCount('bpom_data', fn () => \App\Models\BpomData::where('kategori', 'kosmetik')->count()));
        });
    }
}
