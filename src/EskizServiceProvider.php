<?php

namespace NotificationChannels\EskizSms;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class EskizServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(EskizConfig::class, function ($app) {
            return new EskizConfig(config('services.eskiz'));
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPublishing();
    }


    protected function registerPublishing(): void
    {
        $this->publishes([$this->getConfig() => config_path('eskiz-sms.php')], 'eskiz-config');

    }

    /**
     * Get the config file path.
     */
    protected function getConfig(): string
    {
        return __DIR__.'/../config/eskiz-sms.php';
    }
}
