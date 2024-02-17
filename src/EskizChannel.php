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

        // Получите токен для аутентификации на API Eskiz
        $token = $this->getToken();

        // Отправьте запрос к API Eskiz, используя клиент Guzzle
        $response = $this->client->request('POST', 'https://notify.eskiz.uz/api/message/sms/send', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'form_params' => [
                'mobile_phone' => $message->getTo(),
                'message' => $message->getContent(),
                'from' => $message->getFrom(),
            ],
        ]);

        return $response;
    }

    // Определите метод для получения токена для аутентификации на API Eskiz
    protected function getToken()
    {
        // Получите email и пароль из файла конфигурации
        $config = config('services.eskiz');

        // Отправьте запрос к API Eskiz, используя клиент Guzzle
        $response = $this->client->request('POST', 'https://notify.eskiz.uz/api/auth/login', [
            'form_params' => [
                'email' => $config['email'],
                'password' => $config['password'],
            ],
        ]);

        // Получите тело ответа в виде массива
        $body = json_decode($response->getBody(), true);

        // Верните токен из ответа
        return $body['data']['token'];
    }
}
