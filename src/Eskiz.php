<?php

namespace NotificationChannels\EskizSms;

class Eskiz
{
    /** @var EskizConfig */
    public $config;

    public function __construct(EskizConfig $config)
    {
        $this->config = $config;
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
    public function sendMessage(EskizMessage $message, ?string $to, $from)
    {
        return $message;

    }

        /**
     * Get the from address from message, or config.
     *
     * @param EskizMessage $message
     * @return string|null
     */
    protected function getFrom(EskizMessage $message): ?string
    {
        return $message->getFrom() ?: $this->config->getFrom();
    }



}