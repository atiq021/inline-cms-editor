<?php

namespace SBX\FrontCRM\Providers;

use Illuminate\Support\ServiceProvider;

class CRMServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
        $this->loadViewsFrom(__DIR__.'/../views', 'frontcrm'); 

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateSbxSettingsTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_sbx_settings_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_sbx_settings_table.php'),
                ], 'migrations');
            }
        }
    }
}
