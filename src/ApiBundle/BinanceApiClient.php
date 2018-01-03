<?php

namespace ApiBundle;

use Exception\BinanceApiException;
use GuzzleHttp\Client;

class BinanceApiClient
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

    public function getApiUrl()
    {
        return 'https://api.binance.com';
    }

    /**
     * Test connectivity to the Rest API.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#test-connectivity
     */
    public function ping()
    {
        return $this->_makeApiRequest('GET', 'ping');
    }

    /**
     * Test connectivity to the Rest API and get the current server time.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#check-server-time
     */
    public function getServerTime()
    {
        return $this->_makeApiRequest('GET', 'time');
    }

    /**
     * Returns the order book for the market.
     *
     * @param array $params The data to send.
     * @option string "symbol" The symbol to search for. (required)
     * @option int    "limit"  The number of results returned from the query. (max value 100)
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#order-book
     */
    public function getOrderBook($params)
    {
        return $this->_makeApiRequest('GET', 'depth', 'NONE', $params);
    }

    /**
     * Returns compressed, aggregate trades.
     * Trades that fill at the time, from the same order, with the same price will have the quantity aggregated.
     *
     * @param array $params The data to send.
     * @option string "symbol"    The symbol to search for. (required)
     * @option int    "fromId"    ID to get aggregate trades from INCLUSIVE.
     * @option int    "startTime" Timestamp in milliseconds to get aggregate trades from INCLUSIVE.
     * @option int    "endTime"   Timestamp in milliseconds to get aggregate trades until INCLUSIVE.
     * @option int    "limit"     The number of results returned from the query. (max value 500)
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     *
     * @link https://www.binance.com/restapipub.html#compressedaggregate-trades-list
     */
    public function getAggTrades($params)
    {
        return $this->_makeApiRequest('GET', 'aggTrades', 'NONE', $params);
    }

    /**
     * Returns kline/candlesticks bars for a symbol.
     * Klines are uniquely identified by their open time.
     *
     * @param array $params The data to send.
     * @option string "symbol"    The symbol to search for. (required)
     * @option string "interval"  Kline intervals enum. (required)
     * @option int    "startTime" Timestamp in milliseconds to get aggregate trades from INCLUSIVE.
     * @option int    "endTime"   Timestamp in milliseconds to get aggregate trades until INCLUSIVE.
     * @option int    "limit"     The number of results returned from the query. (max value 500)
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link  https://www.binance.com/restapipub.html#klinecandlesticks
     */
    public function getKlines($params)
    {
        return $this->_makeApiRequest('GET', 'klines', 'NONE', $params);
    }

    /**
     * Returns 24 hour price change statistics.
     *
     * @param array $params The data to send.
     * @option string "symbol" The symbol to search for. (required)
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#24hr-ticker-price-change-statistics
     */
    public function getTwentyFourTickerPrice($params)
    {
        return $this->_makeApiRequest('GET', 'ticker/24hr', 'NONE', $params);
    }

    /**
     * Returns latest price for all symbols.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#symbols-price-ticker
     */
    public function getTickers()
    {
        return $this->_makeApiRequest('GET', 'ticker/allPrices');
    }

    /**
     * Returns price/qty on the order book for all symbols.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#symbols-order-book-ticker
     */
    public function getBookTickers()
    {
        return $this->_makeApiRequest('GET', 'ticker/allBookTickers');
    }

    /**
     * Send in a new order.
     *
     * @param array $params The data to send.
     * @option string "symbol"           The symbol to search for. (required)
     * @option string "side"             Order side enum. (required)
     * @option string "type"             Order type enum. (required)
     * @option string "timeInForce"      Time in force enum. (required)
     * @option double "quantity"         Desired quantity. (required)
     * @option double "price"            Asking price. (required)
     * @option string "newClientOrderId" A unique id for the order. Automatically generated by default.
     * @option double "stopPrice"        Used with STOP orders.
     * @option double "icebergQty"       Used with icebergOrders.
     * @option int    "timestamp"        A UNIX timestamp. (required)
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#new-order--signed
     */
    public function postOrder($params)
    {
        return $this->_makeApiRequest('POST', 'order', 'SIGNED', $params);
    }

    /**
     * Test new order creation and signature/recvWindow long.
     * Creates and validates a new order but does not send it into the matching engine.
     *
     * @param array $params The data to send.
     * @option string "symbol"           The symbol to search for. (required)
     * @option string "side"             Order side enum. (required)
     * @option string "type"             Order type enum. (required)
     * @option string "timeInForce"      Time in force enum. (required)
     * @option double "quantity"         Desired quantity. (required)
     * @option double "price"            Asking price. (required)
     * @option string "newClientOrderId" A unique id for the order. Automatically generated by default.
     * @option double "stopPrice"        Used with STOP orders.
     * @option double "icebergQty"       Used with icebergOrders.
     * @option int    "timestamp"        A UNIX timestamp. (required)
     * @option int    "recvWindow"       The number of milliseconds after timestamp the request is valid for.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#test-new-order-signed
     */
    public function postOrderTest($params)
    {
        return $this->_makeApiRequest('POST', 'order/test', 'SIGNED', $params);
    }

    /**
     * Check an order's status.
     *
     * @param array $params The data to send.
     * @option string "symbol"            The symbol to search for. (required)
     * @option int    "orderId"           The order ID.
     * @option string "origClientOrderId" The original client order ID.
     * @option int    "timestamp"         A UNIX timestamp. (required)
     * @option int    "recvWindow"        The number of milliseconds after timestamp the request is valid for.
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     *
     * @link https://www.binance.com/restapipub.html#query-order-signed
     */
    public function getOrder($params)
    {
        return $this->_makeApiRequest('GET', 'order', 'SIGNED', $params);
    }

    /**
     * Cancel an active order.
     *
     * @param array $params The data to send.
     * @option string "symbol"            The symbol to search for. (required)
     * @option int    "orderId"           The order ID.
     * @option string "origClientOrderId" The original client order ID.
     * @option string "newClientOrderId"  Used to uniquely identify this cancel. Automatically generated by default.
     * @option int    "timestamp"         A UNIX timestamp. (required)
     * @option int    "recvWindow"        The number of milliseconds after timestamp the request is valid for.
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     *
     * @link https://www.binance.com/restapipub.html#cancel-order-signed
     */
    public function cancelOrder($params)
    {
        return $this->_makeApiRequest('DELETE', 'order', 'SIGNED', $params);
    }

    /**
     * Returns all open orders on a symbol.
     *
     * @param array $params The data to send.
     * @option string "symbol"     The symbol to search for. (required)
     * @option int    "timestamp"  A UNIX timestamp. (required)
     * @option int    "recvWindow" The number of milliseconds after timestamp the request is valid for.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#current-open-orders-signed
     */
    public function getOpenOrders($params)
    {
        return $this->_makeApiRequest('GET', 'openOrders', 'SIGNED', $params);
    }

    /**
     * Returns all account orders; active, canceled, or filled.
     *
     * @param array $params The data to send.
     * @option string "symbol"     The symbol to search for. (required)
     * @option int    "orderId"    The order ID.
     * @option int    "timestamp"  A UNIX timestamp. (required)
     * @option int    "limit"      The request limit, max value 500, min value 1.
     * @option int    "recvWindow" The number of milliseconds after timestamp the request is valid for.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#all-orders-signed
     */
    public function getOrders($params)
    {
        return $this->_makeApiRequest('GET', 'allOrders', 'SIGNED', $params);
    }

    /**
     * Returns current account information.
     *
     * @param array $params The data to send.
     * @option int "timestamp"  A UNIX timestamp. (required)
     * @option int "recvWindow" The number of milliseconds after timestamp the request is valid for.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#account-information-signed
     */
    public function getAccount($params)
    {
        return $this->_makeApiRequest('GET', 'account', 'SIGNED', $params);
    }

    /**
     * Returns trades for a specific account and symbol.
     *
     * @param array $params The data to send.
     * @option string "symbol"     The symbol to search for. (required)
     * @option int    "fromId"     The order ID.
     * @option int    "timestamp"  A UNIX timestamp. (required)
     * @option int    "limit"      The number of results returned from the query. (max value 500)
     * @option int    "recvWindow" The number of milliseconds after timestamp the request is valid for.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#account-trade-list-signed
     */
    public function getTrades($params)
    {
        return $this->_makeApiRequest('GET', 'myTrades', 'SIGNED', $params);
    }

    /**
     * Submit a withdraw request.
     *
     * @param array $params The data to send.
     * @option string "asset"      The requested asset. (required)
     * @option string "address"    The request address. (required)
     * @option double "amount"     The request amount. (required)
     * @option string "name"       Description of the address.
     * @option int    "recvWindow" The number of milliseconds after timestamp the request is valid for.
     * @option int    "timestamp"  A UNIX timestamp. (required)
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#account-trade-list-signed
     */
    public function withdraw($params)
    {
        return $this->_makeApiRequest('POSTV2', 'wapi/v1/withdraw.html', 'WAPI_SIGNED', $params);
    }

    /**
     * Fetch deposit history.
     *
     * @param array $params The data to send.
     * @option string "asset"      The requested asset.
     * @option enum   "status"     Enum as WAPI_DEPOSIT_STATUS_*.
     * @option int    "startTime"  Timestamp in milliseconds.
     * @option int    "endTime"    Timestamp in milliseconds.
     * @option int    "recvWindow" The number of milliseconds after timestamp the request is valid for.
     * @option int    "timestamp"  A UNIX timestamp. (required)
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#account-trade-list-signed
     */
    public function getDepositHistory($params)
    {
        return $this->_makeApiRequest('POSTV2', 'wapi/v1/getDepositHistory.html', 'WAPI_SIGNED', $params);
    }

    /**
     * Fetch withdraw history.
     *
     * @param array $params The data to send.
     * @option string "asset"      The requested asset.
     * @option enum   "status"     Enum as WAPI_WITHDRAW_STATUS_*.
     * @option int    "startTime"  Timestamp in milliseconds.
     * @option int    "endTime"    Timestamp in milliseconds.
     * @option int    "recvWindow" The number of milliseconds after timestamp the request is valid for.
     * @option int    "timestamp"  A UNIX timestamp. (required)
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#account-trade-list-signed
     */
    public function getWithdrawHistory($params)
    {
        return $this->_makeApiRequest('POSTV2', 'wapi/v1/getWithdrawHistory.html', 'WAPI_SIGNED', $params);
    }

    /**
     * Start a new user data stream.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#start-user-data-stream-api-key
     */
    public function startUserDataStream()
    {
        return $this->_makeApiRequest('POST', 'userDataStream', 'API-KEY');
    }

    /**
     * PING a user data stream to prevent a time out.
     *
     * @param array $params The data to send.
     * @option string "listenKey" The key for the user's data steam. (required)
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#keepalive-user-data-stream-api-key
     */
    public function keepaliveUserDataStream($params)
    {
        return $this->_makeApiRequest('PUT', 'userDataStream', 'API-KEY', $params);
    }

    /**
     * Close out a user data stream.
     *
     * @param array $params The data to send.
     * @option string "listenKey" The key for the user's data steam. (required)
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @link https://www.binance.com/restapipub.html#close-user-data-stream-api-key
     */
    public function closeUserDataStream($params)
    {
        return $this->_makeApiRequest('DELETE', 'userDataStream', 'API-KEY', $params);
    }

    /**
     * Does an HTTP request to the given endpoint with the given parameters.
     *
     * @param string $type The HTTP method.
     * @param string $endPoint The API endpoint.
     * @param string $securityType Security type enum.
     * @param array $params Additional parameters.
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws BinanceApiException
     */
    private function _makeApiRequest($type, $endPoint, $securityType = 'NONE', $params = [])
    {
        $params = array_filter($params, 'strlen');
        switch (strtoupper($securityType)) {
            default:
            case 'NONE':
                $client = new Client(['http_errors' => false]);
                $url = $this->getApiUrl() . '/api/v1/' . $endPoint;
                break;
            case 'API-KEY':
                $client = new Client(['headers' => ['X-MBX-APIKEY' => $this->getApiKey()], 'http_errors' => false]);
                $url = $this->getApiUrl() . '/api/v1/' . $endPoint;
                break;
            case 'SIGNED':
                $client = new Client(['headers' => ['X-MBX-APIKEY' => $this->getApiKey()], 'http_errors' => false]);
                $url = $this->getApiUrl() . '/api/v3/' . $endPoint;
                $params['signature'] = hash_hmac('sha256', http_build_query($params), $this->getApiSecret());
                break;
            case 'WAPI_SIGNED':
                $client = new Client(['headers' => ['X-MBX-APIKEY' => $this->getApiKey()], 'http_errors' => false]);
                $url = $this->getApiUrl() . $endPoint;
                $params['signature'] = hash_hmac('sha256', http_build_query($params), $this->getApiSecret());
                break;
        }
        switch (strtoupper($type)) {
            default:
            case 'GET':
                $params['query'] = $params;
                break;
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $params['form_params'] = $params;
                break;
            case 'POSTV2':
                $type = 'POST';
                $params['query'] = $params;
                break;
        }
        $response = $client->request(strtoupper($type), $url, $params);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw new BinanceApiException($response->getBody()->getContents());
        }
        return $response;
    }
}