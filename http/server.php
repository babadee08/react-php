<?php

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Socket\Server;

require "vendor/autoload.php";

$loop = Factory::create();

$posts = require "posts.php";

$server = new \React\Http\Server(function (ServerRequestInterface $request) use ($posts) {
    $params = $request->getQueryParams();
    $tag = $params['tag'] ?? null;

    $filteredPosts = array_filter($posts, function ($post) use ($tag) {
        if ($tag) {
            return in_array($tag, $post['tags']);
        }
        return true;
    });


    $page = $params['page'] ?? 1;
    $filteredPosts = array_chunk($filteredPosts, 3);
    $filteredPosts = $filteredPosts[$page - 1] ?? [];

    echo 'Request to ' . $request->getUri() . PHP_EOL;
    /*$body = "The method of the request is: " . $request->getMethod();
    $body .= "The requested path is: " . $request->getUri()->getPath();
    return new Response(200, ['Content-Type' => 'text/plain'], $body);*/
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($filteredPosts));
});

// $socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$socket = new Server(8000, $loop);

$server->listen($socket);

$loop->run();