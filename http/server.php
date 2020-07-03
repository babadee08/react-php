<?php

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Socket\Server;

require "vendor/autoload.php";

$loop = Factory::create();

$server = new \React\Http\Server(function (ServerRequestInterface $request) {
    echo 'Request to ' . $request->getUri() . PHP_EOL;
    $body = "The method of the request is: " . $request->getMethod();
    $body .= "The requested path is: " . $request->getUri()->getPath();
    return new Response(200, ['Content-Type' => 'text/plain'], $body);
});

// $socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$socket = new Server(8000, $loop);

$server->listen($socket);

$loop->run();