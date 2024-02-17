<?php

namespace App\Providers;

use App\Channels\EskizChannel;
use App\Notifications\EskizMessage;
use Illuminate\Support\ServiceProvider;
use Uzsoftic\LaravelEskiz\Eskiz;
use GuzzleHttp\Client;

class EskizServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрируйте экземпляр Eskiz в контейнере сервисов
        $this->app->singleton(Eskiz::class, function ($app) {
            // Получите email и пароль из файла конфигурации
            $email = config('services.eskiz.email');
            $password = config('services.eskiz.password');

            // Создайте новый экземпляр Eskiz, передавая email и пароль
            return new Eskiz($email, $password);
        });

        // Регистрируйте экземпляр EskizChannel в контейнере сервисов
        $this->app->singleton(EskizChannel::class, function ($app) {
            // Получите экземпляр Eskiz из контейнера сервисов
            $eskiz = $app->make(Eskiz::class);

            // Создайте новый экземпляр EskizChannel, передавая экземпляр Eskiz и клиент Guzzle
            return new EskizChannel($eskiz, new Client());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Опубликуйте файл конфигурации Eskiz в папку config
        $this->publishes([
            __DIR__.'/../config/eskiz.php' => config_path('eskiz.php'),
        ]);

        // Добавьте псевдоним eskiz для канала EskizChannel
        $this->app->make('Illuminate\Notifications\ChannelManager')
            ->extend('eskiz', function ($app) {
                return $app->make(EskizChannel::class);
            });

        // Добавьте макрос eskiz для класса Notification, который возвращает экземпляр EskizMessage
        Notification::macro('eskiz', function ($message) {
            return new EskizMessage($message);
        });
    }
}
