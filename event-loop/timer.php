<?php

require "vendor/autoload.php";

$loop = \React\EventLoop\Factory::create();

$counter = 0;
/*$timer = $loop->addPeriodicTimer(1, function () use (&$counter, &$timer, $loop) {

    $counter++;

    echo "Hello \n";

    if ($counter === 5) {
        $loop->cancelTimer($timer);
    }
});*/

$loop->addPeriodicTimer(1, function (\React\EventLoop\TimerInterface $timer) use (&$counter, $loop) {

    $counter++;

    echo "Hello \n";

    if ($counter === 5) {
        $loop->cancelTimer($timer);
    }
});

$loop->run();