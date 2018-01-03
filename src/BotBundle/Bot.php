<?php

namespace BotBundle;

use ApiBundle\AbstractClient;
use ApiBundle\IClient;

class Bot
{
    private $client;

    public function __construct(IClient $client)
    {
        $this->client = $client;
    }

    public function run()
    {
        if (!$this->client->ping()) {
            throw new \Exception('Ping failed');
        }

        while (true) {
            $this->tick();
//            sleep(1);
            $this->client->ping();
        }
    }

    private function tick()
    {
        echo time() . '<br/>';
    }
}