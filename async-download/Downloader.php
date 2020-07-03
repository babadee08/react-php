<?php


use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use React\Filesystem\Filesystem;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;
use function React\Promise\Stream\unwrapWritable;

final class Downloader
{
    /**
     * @var Browser
     */
    private $client;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $dir;

    public function __construct(Browser $client, Filesystem $filesystem, string $dir)
    {
        $this->client = $client;
        $this->filesystem = $filesystem;
        $this->dir = $dir;
    }

    public function download(string ...$urls)
    {
        foreach ($urls as $url) {
            /*$this->openFile($url)->then(function (WritableStreamInterface $file) use ($url) {
                $this->client->requestStreaming('GET', $url)->then(function (ResponseInterface $response) use ($file) {
                    $body = $response->getBody();

                    assert($body instanceof \Psr\Http\Message\StreamInterface);
                    assert($body instanceof \React\Stream\ReadableStreamInterface);

                    $body->pipe($file);
                });
            });*/
            $file = $this->openFile($url);
            $this->client->requestStreaming('GET', $url)->then(function (ResponseInterface $response) use ($file) {
                $body = $response->getBody();

                assert($body instanceof StreamInterface);
                assert($body instanceof ReadableStreamInterface);

                $body->pipe($file);
            });
        }
    }

    private function openFile(string $url) : WritableStreamInterface
    {
        $path = $this->dir . DIRECTORY_SEPARATOR . basename($url);

        return unwrapWritable($this->filesystem->file($path)->open('cw'));
    }
}