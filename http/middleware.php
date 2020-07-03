<?php

use FriendsOfReact\Http\Middleware\Psr15Adapter\GroupedPSR15Middleware;
use FriendsOfReact\Http\Middleware\Psr15Adapter\PSR15Middleware;
use Middlewares\ClientIp;
use Middlewares\Redirect;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Socket\Server;

require "vendor/autoload.php";
require "Logging.php";
require "CustomHeader.php";

$loop = Factory::create();

$redirect = function (ServerRequestInterface $request, callable $next) {
    echo "Got here 'redirect' \n";
    if ($request->getUri()->getPath() === '/admin') {
        return new Response(301, ['Location' => '/']);
    }
    return $next($request);
};

$hello = function (ServerRequestInterface $request) {
    echo "Got here 'hello' \n";
    $body = "The method of the request is: " . $request->getMethod();
    $body .= "The requested path is: " . $request->getUri()->getPath();

    return new Response(200, ['Content-Type' => 'text/plain'], $body);
};

$server = new \React\Http\Server([
    // new PSR15Middleware($loop, Redirect::class, [['/secret' => '/']]),
    // new PSR15Middleware($loop, ClientIp::class),
    (new GroupedPSR15Middleware($loop))
        ->withMiddleware(ClientIp::class)
        ->withMiddleware(Redirect::class, [['/secret' => '/']]),
    new Logging(),
    new CustomHeader('X-Custom', 'foo'),
    $redirect,
    $hello
]);

// $socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$socket = new Server(8000, $loop);

$server->listen($socket);

$loop->run();