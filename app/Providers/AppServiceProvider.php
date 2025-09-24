<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Nossas novas linhas:
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Nosso novo código:
        $this->app->singleton(ImageManager::class, function ($app) {
            // Diz ao Laravel para criar o ImageManager usando o Driver do GD.
            return new ImageManager(new Driver());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
