<?php

namespace App\Helpers;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Websocket implements MessageComponentInterface
{
    protected \SplObjectStorage $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        // Log::info("new connection! $conn->resourceId \n");
        echo("new connection! $conn->resourceId \n");
    }

    // логика отправки сообщений
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        // Log::info("new connection! $conn->resourceId \n");

        echo sprintf('Connection %d sending message "%s" to %d other connection %s' . "\n", $from->resourceId, $msg, $numRecv, $numRecv === 1);

        foreach ($this->clients as $client) {
            if ($from != $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        echo("connection $conn->resourceId  has disconnected\n");
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occured: {$e->getMessage()}\n";
        $conn->close();
    }
}
