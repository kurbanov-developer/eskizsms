<?php

namespace NotificationChannels\EskizSms;

use GuzzleHttp\Client;

class Eskiz
{

    public function __construct()
    {
        //
    }
    /**
     * Send a EskizMessage to the a phone number.
     *
     * @param EskizMessage $message
     * @param string|null $to
     * @param bool $useAlphanumericSender
     *
     * @return mixed
     */
    public function sendMessage(string $message, string $to, ?string $from)
    {
        $client = new Client();
        $to = preg_replace('/[^0-9]/', '', $to);
        $token = $this->getToken();
        $response = $client->request('POST', 'https://notify.eskiz.uz/api/message/sms/send', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'form_params' => [
                'mobile_phone' => $to,
                'message' => $message,
                'from' => config('services.eskiz.from'),
            ],
        ]);

    }


    public function getToken()
    {
        $client = new Client();
        // Получите email и пароль из файла конфигурации
        $config = config('services.eskiz');
        // Отправьте запрос к API Eskiz, используя клиент Guzzle
        $response = $client->request('POST', 'https://notify.eskiz.uz/api/auth/login', [
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
