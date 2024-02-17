<?php

namespace NotificationChannels\EskizSms;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use NotificationChannels\EskizSms\EskizChannel;
use NotificationChannels\EskizSms\EskizMessage;
use NotificationChannels\EskizSms\EskizConfig;
use NotificationChannels\EskizSms\Eskiz;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class EskizServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->mergeConfigFrom(__DIR__.'/../config/eskizsms.php', 'eskizsms');

        $this->publishes([
            __DIR__.'/../config/eskizsms.php' => config_path('eskizsms.php'),
        ]);

        $this->app->bind(EskizConfig::class, function () {
            return new EskizConfig($this->app['config']['eskizsms']);
        });

        // Регистрируйте экземпляр EskizChannel в контейнере сервисов
        $this->app->singleton(Eskiz::class, function (Application $app) {
            return new Eskiz(
                $app->make(EskizConfig::class)
            );
        });

        $this->app->singleton(EskizChannel::class, function (Application $app) {
            return new EskizChannel(
                $app->make(Eskiz::class),
                $app->make(Dispatcher::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    }

    public function provides(): array
    {
        return [
            EskizConfig::class,
            Eskiz::class,
            EskizChannel::class,
        ];
    }
}
