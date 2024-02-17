<?php

namespace NotificationChannels\EskizSms;

class EskizMessage
{
    // Определите свойства для хранения номера телефона и текста сообщения
    protected $to;
    protected $content;

    // Создайте новый экземпляр сообщения, принимая номер телефона и текст сообщения в качестве параметров
    public function __construct($to = null, $content = null)
    {
        $this->to = $to;
        $this->content = $content;
    }

    // Определите метод для установки номера телефона
    public function to($to)
    {
        $to = preg_replace('/[^0-9]/', '', $to);

        $this->to = $to;
        return $this;
    }


    // Определите метод для установки текста сообщения
    public function content($content)
    {
        $this->content = $content;
        return $this;
    }

    // Определите метод для получения номера телефона
    public function getTo()
    {
        return $this->to;
    }

    // Определите метод для получения текста сообщения
    public function getContent()
    {
        return $this->content;
    }
}
