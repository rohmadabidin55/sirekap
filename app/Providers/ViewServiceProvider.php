<?php

namespace App\Providers;

use App\Models\Sekolah;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
    public function boot()
    {
        // Bagikan data sekolah ke layout admin, guru, dan halaman login
        // Pengecekan Schema::hasTable penting agar migrasi tidak error
        if (Schema::hasTable('sekolah')) {
            View::composer(['layouts.admin', 'layouts.guru', 'auth.login'], function ($view) {
                $sekolah = Sekolah::find(1);
                $view->with('sekolahSetting', $sekolah);
            });
        }
    }
}
