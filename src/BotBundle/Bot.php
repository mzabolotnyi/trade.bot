<?php

namespace BotBundle;

use ApiBundle\AbstractClient;

class Bot
{
    private $client;

    public function __construct(AbstractClient $client)
    {
        $this->client = $client;
    }

    public function run()
    {
        while (true) {
            $this->tick();
            sleep(1);
        }
    }

    private function tick()
    {
        echo time() . '<br/>';
    }
}