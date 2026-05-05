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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
