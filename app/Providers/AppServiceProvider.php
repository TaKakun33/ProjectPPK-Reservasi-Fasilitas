<?php

namespace App\Providers;

use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Models\Facility;
use App\Models\ReportCategory;
use App\Models\User;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Database\Eloquent\Model;
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
        // Deteksi N+1 query: lazy loading dicatat ke log (bukan dilempar sebagai error) di luar production,
        // sehingga pelanggaran terlihat saat pengembangan tanpa merusak halaman.
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation): void {
            logger()->warning(sprintf('N+1 terdeteksi: lazy loading [%s] pada model [%s].', $relation, $model::class));
        });

        // Data untuk pop-up formulir: dimuat hanya saat komponen pop-up dirender,
        // memakai aturan yang sama dengan halaman penuh di controller masing-masing.
        View::composer('components.modal-reservasi', function (ViewContract $view): void {
            $view->with('facilities', Facility::where('facility_status', 'aktif')->orderBy('facility_name')->get());
        });

        View::composer('components.modal-laporan', function (ViewContract $view): void {
            $view->with([
                'facilities' => Facility::visible()->orderBy('facility_name')->get(),
                'categories' => ReportCategory::where('is_active', true)->orderBy('category_name')->get(),
            ]);
        });

        // Badge jumlah akun menunggu verifikasi di sidebar admin (sebelumnya variabel ini tidak pernah di-set).
        View::composer('components.admin-layout', function (ViewContract $view): void {
            $view->with('adminPendingCount', User::where('account_status', 'pending')->count());
        });

        View::composer('components.modal-fasilitas', function (ViewContract $view): void {
            $view->with(app(AdminFacilityController::class)->opsiIsian());
        });
    }
}
