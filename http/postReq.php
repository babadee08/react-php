<?php

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Socket\Server;

require "vendor/autoload.php";

$loop = Factory::create();

$posts = [];

$server = new \React\Http\Server(function (ServerRequestInterface $request) use (&$posts) {
    $path = $request->getUri()->getPath();
    if ($path === '/store') {
        // this is useful for parsing form data
        // $posts[] = $request->getParsedBody();

        $posts[] = json_decode((string)$request->getBody(), true);

        return new Response(201);
    }

    return new Response(200, ['Content-Type' => 'application/json'], json_encode($posts));
});

$socket = new Server(8000, $loop);

$server->listen($socket);

$loop->run();