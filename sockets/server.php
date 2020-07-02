<?php

use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use React\Socket\Server;

require "vendor/autoload.php";

require "ConnectionsPool.php";

$loop = Factory::create();

$socket = new Server('127.0.0.1:8080', $loop);

$pool = new ConnectionsPool();

$socket->on('connection', function (ConnectionInterface $connection) use ($pool) {
    $pool->add($connection);
    /*$connection->write("Hello " . $connection->getRemoteAddress() . "!\n");
    $connection->write("Welcome to this amazing server!\n");
    $connection->write("Here's a tip: don't say anything.\n");

    $connection->on('data', function ($data) use ($connection) {
        $connection->write($data);
        // $connection->close();
    });*/
});

$loop->run();