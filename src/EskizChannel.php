<?php

namespace NotificationChannels\EskizSms;

use Illuminate\Notifications\Notification;
use GuzzleHttp\Client;

class EskizChannel
{
    // Определите свойство для хранения клиента Guzzle
    protected $client;

    protected $eskiz;

    // Создайте новый экземпляр канала, принимая клиента Guzzle в качестве параметра
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function refreshToken()
    {
        // Получите токен из свойства eskiz
        $token = $this->eskiz->getToken();

        // Отправьте запрос к API Eskiz, используя клиент Guzzle, чтобы обновить токен
        $response = $this->client->request('PATCH', 'https://notify.eskiz.uz/api/auth/refresh', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        // Получите тело ответа в виде массива
        $body = json_decode($response->getBody(), true);

        // Получите новый токен из ответа
        $new_token = $body['data']['token'];

        // Установите новый токен в свойство eskiz
        $this->eskiz->setToken($new_token);
    }

    // Определите, как должно быть отправлено уведомление по каналу Eskiz
    public function send($notifiable, Notification $notification)
    {
        // Получите представление уведомления для канала Eskiz
        $message = $notification->toEskiz($notifiable);

        $config = $app->make(EskizConfig::class);

        // Получите номер телефона для получения SMS
        $to = $notifiable->routeNotificationFor('eskiz', $notification);

        // Получите токен для аутентификации на API Eskiz
        $token = $this->getToken();

        // Отправьте запрос к API Eskiz, используя клиент Guzzle
        $this->client->request('POST', 'https://notify.eskiz.uz/api/message/sms/send', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'form_params' => [
                'mobile_phone' => $to,
                'message' => $message,
                'from' => $from,
            ],
        ]);

        $this->eskiz->messages->create($to, $message);
    }

    // Определите метод для получения токена для аутентификации на API Eskiz
    protected function getToken()
    {
        // Получите email и пароль из файла конфигурации
        $config = $app->make(EskizConfig::class);

        // Отправьте запрос к API Eskiz, используя клиент Guzzle
        $response = $this->client->request('POST', 'https://notify.eskiz.uz/api/auth/login', [
            'form_params' => [
                'email' => $config->getEmail(),
                'password' => $config->getPassword(),
            ],
        ]);

        // Получите тело ответа в виде массива
        $body = json_decode($response->getBody(), true);

        // Верните токен из ответа
        return $body['data']['token'];
    }
}