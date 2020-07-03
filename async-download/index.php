<?php

use Clue\React\Buzz\Browser;
use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require "vendor/autoload.php";
require "Downloader.php";

$loop = Factory::create();
$client = new Browser($loop);

/*$client->get('http://www.google.com/')->then(function (\Psr\Http\Message\ResponseInterface $response) {
    var_dump($response->getHeaders(), (string)$response->getBody());
});*/

/*$client->requestStreaming('GET', 'http://www.google.com/')
    ->then(
        function (\Psr\Http\Message\ResponseInterface $response) {
            $body = $response->getBody();
            assert($body instanceof \Psr\Http\Message\StreamInterface);
            assert($body instanceof \React\Stream\ReadableStreamInterface);

            $body->on('data', function ($chunk) {
                echo $chunk;
            });
        }
    );*/
$downloader = new Downloader($client, Filesystem::create($loop), __DIR__ . '/downloads');

$downloader->download(... [
    'http://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4',
    'http://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_2mb.mp4',
    'http://sample-videos.com/video123/mp4/480/big_buck_bunny_480p_1mb.mp4',
    'http://sample-videos.com/video123/mp4/360/big_buck_bunny_360p_2mb.mp4'
]);

$loop->run();
