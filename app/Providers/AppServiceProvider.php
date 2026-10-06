<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
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
        // Deteksi N+1 query: lazy loading dicatat ke log (bukan dilempar sebagai error) di luar production,
        // sehingga pelanggaran terlihat saat pengembangan tanpa merusak halaman.
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation): void {
            logger()->warning(sprintf('N+1 terdeteksi: lazy loading [%s] pada model [%s].', $relation, $model::class));
        });
    }
}
