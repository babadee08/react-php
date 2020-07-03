<?php

require "vendor/autoload.php";

$loop = \React\EventLoop\Factory::create();

$connector = new \React\Socket\Connector($loop);
$input = new \React\Stream\ReadableResourceStream(STDIN, $loop);
$output = new \React\Stream\WritableResourceStream(STDOUT, $loop);

$connector->connect('127.0.0.1:8080')
    ->then(function (\React\Socket\ConnectionInterface $connection) use ($input, $output) {
        /*$input->on('data', function ($data) use ($connection) {
            $connection->write($data);
        });*/
        // This is a replacement for the code above
        // $input->pipe($connection);

        /*$connection->on('data', function ($data) use ($output) {
            // echo $data;
            $output->write($data);
        });*/
        // Replaces the code above
        // $connection->pipe($output);

        // This simplifies the entire code above
        $input->pipe($connection)->pipe($output);
    }, function (Exception $exception) {
        echo $exception->getMessage();
    });

$loop->run();