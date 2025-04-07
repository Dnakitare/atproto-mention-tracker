<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AlertService::class, function ($app) {
            return new AlertService();
        });
        
        $this->app->bind(SentimentAnalysisService::class, function ($app) {
            return new SentimentAnalysisService();
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
