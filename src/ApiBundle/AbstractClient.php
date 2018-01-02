<?php

namespace ApiBundle;

abstract class AbstractClient implements IClient
{
    private $apiKey;
    private $apiSecret;

    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    protected function getApiKey()
    {
        return $this->apiKey;
    }

    protected function getApiSecret()
    {
        return $this->apiSecret;
    }
}