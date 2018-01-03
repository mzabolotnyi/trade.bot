<?php

namespace ApiBundle;

class ClientFactory
{
    public static function create(array $config)
    {
        $platform = $config['tradingPlatform'];
        $apiKey = $config['apiKey'];
        $apiSecret = $config['apiSecret'];

        switch ($platform) {
            case 'binance':
                $client = new BinanceClient(new BinanceApiClient($apiKey, $apiSecret));
                break;
            default:
                throw new \Exception('Api client for platform "' . $platform . '" not implement');
        }

        return $client;
    }
}