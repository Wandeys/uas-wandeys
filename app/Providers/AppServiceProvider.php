<?php

namespace App\Providers;

use App\Models\Setting;
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
        $setting = null;
        try {
            $setting = Setting::first();
        } catch (\Exception $e) {
            // database tidak ditemukan atau belum migrasi
        }

        if (!$setting) {
            $setting = new Setting([
                'app_name' => 'SIMANA',
                'copyright' => 'SIMANA © 2026',
                'login_title' => 'Selamat Datang',
                'keywords' => 'simana, akademik, nilai',
                'description' => 'Sistem Manajemen Nilai Akademik',
                'logo' => null,
            ]);
        }

        View::share('setting', $setting);
    }
}
