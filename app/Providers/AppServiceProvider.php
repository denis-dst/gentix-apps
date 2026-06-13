<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        // Force HTTPS scheme based on APP_URL setting
        // If APP_URL starts with https://, force HTTPS for all generated URLs
        // This respects the deployment environment - Laragon uses http://, production uses https://
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Superadmin') ? true : null;
        });

        if (!app()->runningInConsole()) {
            try {
                $settings = \App\Models\Setting::pluck('value', 'key')->all();
                view()->share('global_settings', $settings);
            } catch (\Exception $e) {
                // Table might not exist yet during initial setup
            }
        }
    }
}
