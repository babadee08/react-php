<?php

require "vendor/autoload.php";

/*function http($url, $method, callable $onSuccess, callable $onError) {
    $response = 'data';

    if ($response) {
        $onSuccess($response);
    } else {
        $onError(new Exception('No response'));
    }
}

http('http"//google.com', 'GET', function ($response) {
    echo $response . PHP_EOL;
}, function (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
});*/

function http($url, $method)
{
    $response = null;
    $deferred = new \React\Promise\Deferred();

    if ($response) {
        $deferred->resolve($response);
    } else {
        $deferred->reject(new Exception('No response'));
    }

    return $deferred->promise();
}

http('http"//google.com', 'GET')
    ->then(function ($response) {
        return strtoupper($response);
    })
    ->then(
        function ($response) {
            echo $response . PHP_EOL;
        }
    )->otherwise(
        function (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    );