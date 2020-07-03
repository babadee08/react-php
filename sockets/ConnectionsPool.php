<?php


use Colors\Color;
use React\Socket\ConnectionInterface;

class ConnectionsPool
{
    /**
     * @var SplObjectStorage
     */
    private $connections;

    /**
     * ConnectionsPool constructor.
     */
    public function __construct()
    {
        $this->connections = new SplObjectStorage();
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function add(ConnectionInterface $connection) : void
    {
        $connection->write((new Color("Welcome to the Chat \n"))->fg('green'));
        $connection->write('Enter your name: ');
        // $this->connections->attach($connection);
        // $this->sendAll("A new user has Joined the Chat \n", $connection);
        $this->setConnectionName($connection, '');

        $this->initEvent($connection);
    }

    private function getConnectionName(ConnectionInterface $connection)
    {
        return $this->connections->offsetGet($connection);
    }

    private function setConnectionName(ConnectionInterface $connection, $name)
    {
        $this->connections->offsetSet($connection, $name);
    }

    /**
     * @param string $message
     * @param ConnectionInterface $connection
     */
    private function sendAll(string $message, ConnectionInterface $connection) : void
    {
        foreach ($this->connections as $conn) {
            if ($conn !== $connection) {
                $conn->write($message);
            }
        }
    }

    /**
     * @param $data
     * @param ConnectionInterface $connection
     */
    private function addNewMember($data, ConnectionInterface $connection): void
    {
        $name = str_replace(["\n", "\r"], '', $data);
        $this->setConnectionName($connection, $name);
        $this->sendAll((new Color("User $name joined the chat\n"))->fg('blue'), $connection);
    }

    /**
     * @param ConnectionInterface $connection
     */
    private function initEvent(ConnectionInterface $connection): void
    {
        $connection->on('data', function ($data) use ($connection) {
            $name = $this->getConnectionName($connection);
            if (empty($name)) {
                $this->addNewMember($data, $connection);
                return;
            }
            $this->sendAll((new Color("$name: "))->bold() .  $data, $connection);
        });

        $connection->on('close', function () use ($connection) {
            $name = $this->getConnectionName($connection);
            $this->connections->detach($connection);
            $this->sendAll((new Color("A user $name has left the chatroom \n"))->fg('red'), $connection);
        });
    }
}