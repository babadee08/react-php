<?php

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Http\Server;
use function FastRoute\simpleDispatcher;

require "vendor/autoload.php";

$loop = Factory::create();

$tasks = [
    'Go to the market',
];

$listTasks = function () use (&$tasks) {
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($tasks));
};

$addNewTask = function (ServerRequestInterface $request) use (&$tasks) {
    $newTask = $request->getParsedBody()['task'] ?? null;
    if ($newTask) {
        $tasks[] = $newTask;
        // array_push($tasks, $newTask);
        print_r($tasks);
        return new Response(201);
    }

    return new Response(
        400,
        ['Content-Type' => 'application/json'],
        json_encode(['error' => 'task field is required'])
    );
};

$viewTask = function (ServerRequestInterface $request, $id) use (&$tasks) {
    return isset($tasks[$id])
        ? new Response(200, ['Content-Type' => 'application/json'], json_encode($tasks[$id]))
        : new Response(404);
};

$dispatcher = simpleDispatcher(
    function (RouteCollector $routes) use ($listTasks, $addNewTask, $viewTask) {
        $routes->get('/tasks/{id:\d+}', $viewTask);
        $routes->get('/tasks', $listTasks);
        $routes->post('/tasks', $addNewTask);
    }
);

$server = new Server(function (ServerRequestInterface $request) use ($dispatcher) {
    $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

    switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
            return new Response(404);
        case Dispatcher::FOUND:
            return $routeInfo[1]($request, ... array_values($routeInfo[2]));
    }
});

$socket = new \React\Socket\Server(8080, $loop);
$server->listen($socket);
echo 'Listening on '.  str_replace('tcp', 'http', $socket->getAddress()) . PHP_EOL;
$loop->run();