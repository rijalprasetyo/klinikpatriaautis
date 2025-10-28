<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

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
            // Untuk kompatibilitas MySQL 5.7
            Schema::defaultStringLength(191);

            if (!App::environment('production') && str_contains(request()->getHost(), 'ngrok-free.dev')) {
                    URL::forceScheme('https');
                }

            // Tambahkan ini untuk ganti collation ke versi lama
            Schema::defaultMorphKeyType('uuid');
            config(['database.connections.mysql.collation' => 'utf8mb4_unicode_ci']);
            config(['database.connections.mysql.charset' => 'utf8mb4']);
        }
}
