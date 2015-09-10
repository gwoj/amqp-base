<?php

use Amqp\Adapter\ExtAdapter;
use Amqp\Consumer;
use Amqp\Message\MessageInterface;
use Amqp\Publisher;

require_once __DIR__ . '/../vendor/autoload.php';
$config = require_once __DIR__ . '/config.php';

$adapter = new ExtAdapter($config);

$publisher = new Publisher();
$publisher->setAdapter($adapter);
$routing_keys = ['foo', 'bar', 'foo.bar', 'bar.foo', null];

for ($i = 0; $i < 10; $i++) {
    $msg = new Amqp\Message();
    $msg->setPayload('Message ' . $i);
    $publisher->publish('global', $msg, $routing_keys[rand(0, count($routing_keys) - 1)]);
}


$consumer = new Consumer();
$consumer->setAdapter($adapter);
$consumer->listen('debug', function (MessageInterface $msg) {
    print_r([
        'payload'    => $msg->getPayload(),
        'properties' => $msg->getProperties(),
        'headers'    => $msg->getHeaders(),
        'delivery-mode' => $msg->getDeliveryMode()
    ]);
    echo PHP_EOL;
});
