<?php


use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

final class CustomHeader
{
    private $title;
    private $value;

    public function __construct($title, $value)
    {
        $this->title = $title;
        $this->value = $value;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        /** @var Response $response */
        $response = $next($request);

        return $response->withHeader($this->title, $this->value);
    }
}