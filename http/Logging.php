<?php


use Psr\Http\Message\ServerRequestInterface;

final class Logging
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        echo 'Method: ' . $request->getMethod() .  '   path: ' . $request->getUri()->getPath() . PHP_EOL;
        return $next($request);
    }
}