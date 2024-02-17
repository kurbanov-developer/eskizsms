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
        if ($message instanceof EskizMessage) {
            if ($from) {
                $message->from($sender);
            }

            return $this->sendSmsMessage($to, $message, $from);
        }

    }

    protected function sendSmsMessage(TwilioSmsMessage $message, ?string $to): MessageInstance
    {
        $debugTo = $this->config->getDebugTo();

        if (!empty($debugTo)) {
            $to = $debugTo;
        }

        $params = [
            'body' => trim($message->content),
        ];

        if ($messagingServiceSid = $this->getMessagingServiceSid($message)) {
            $params['messagingServiceSid'] = $messagingServiceSid;
        }

        if ($this->config->isShortenUrlsEnabled()) {
            $params['ShortenUrls'] = "true";
        }

        if ($from = $this->getFrom($message)) {
            $params['from'] = $from;
        }

        if (empty($from) && empty($messagingServiceSid)) {
            throw CouldNotSendNotification::missingFrom();
        }

        $this->fillOptionalParams($params, $message, [
            'statusCallback',
            'statusCallbackMethod',
            'applicationSid',
            'forceDelivery',
            'maxPrice',
            'provideFeedback',
            'validityPeriod',
        ]);

        if ($message instanceof TwilioMmsMessage) {
            $this->fillOptionalParams($params, $message, [
                'mediaUrl',
            ]);
        }

        return $this->twilioService->messages->create($to, $params);
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