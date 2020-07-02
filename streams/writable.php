<?php

require "vendor/autoload.php";

$loop = \React\EventLoop\Factory::create();

$readable = new \React\Stream\ReadableResourceStream(STDIN, $loop, 1);
$writeable = new \React\Stream\WritableResourceStream(STDOUT, $loop, 1);
$toUpper = new \React\Stream\ThroughStream(function ($chunk) {
    return strtoupper($chunk);
});

$readable->pipe($toUpper)->pipe($writeable);

/*$readable->on('data', function ($chunk) use ($writeable) {
    $writeable->write($chunk);
});*/

// $writeable->write('Hello');

$loop->run();