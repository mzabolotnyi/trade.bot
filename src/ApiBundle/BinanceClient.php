<?php

namespace ApiBundle;

class BinanceClient implements IClient
{
    private $apiClient;

    public function __construct(BinanceApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function ping()
    {
        try {
            $this->apiClient->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}