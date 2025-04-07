<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Alert;
use App\Policies\AlertPolicy;
use App\Models\Mention;
use App\Policies\MentionPolicy;
use App\Models\NotificationSetting;
use App\Policies\NotificationSettingPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }

    protected function registerPolicies()
    {
        $this->app->bind(AlertPolicy::class, function ($app) {
            return new AlertPolicy();
        });

        $this->app->bind(Alert::class, function ($app) {
            return new Alert();
        });

        $this->app->bind(MentionPolicy::class, function ($app) {
            return new MentionPolicy();
        });

        $this->app->bind(NotificationSettingPolicy::class, function ($app) {
            return new NotificationSettingPolicy();
        });

        $this->app->bind(NotificationSetting::class, function ($app) {
            return new NotificationSetting();
        });
    }

    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Alert::class => AlertPolicy::class,
        Mention::class => MentionPolicy::class,
        NotificationSetting::class => NotificationSettingPolicy::class,
    ];
} 