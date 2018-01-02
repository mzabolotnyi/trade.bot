<?php

include_once 'config.php';

var_dump($config);

$bot = new \BotBundle\Bot();
$bot->run();