<?php

namespace NotificationChannels\EskizSms;

class EskizConfig
{
    /** @var array */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getUsername(): ?string
    {
        return $this->config['email'] ?? null;
    }

    public function getPassword(): ?string
    {
        return $this->config['password'] ?? null;
    }

    public function getFrom(): ?string
    {
        return $this->config['from'] ?? null;
    }
}
