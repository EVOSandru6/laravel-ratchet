<?php

namespace App\Helpers;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Websocket implements MessageComponentInterface
{
    protected \SplObjectStorage $clients;

    protected array $rooms;
    protected array $users;
    protected array $users_name; // wtf

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
        // $numRecv = count($this->clients) - 1;
        // Log::info("new connection! $conn->resourceId \n");
        // echo sprintf('Connection %d sending message "%s" to %d other connection %s' . "\n", $from->resourceId, $msg, $numRecv, $numRecv === 1);

        $payload = json_decode($msg);

        // dump($payload->message);

        if ($payload->message === 'new room') {
            $this->rooms[$payload->value][$from->resourceId] = $from;
            $this->users[$from->resourceId] = $payload->value; // в какой комнате он находится
            $this->users_name[$payload->value][$from->resourceId] = $payload->user;
            // dd($this->users);

            $this->notifyUsers($payload);
        } elseif ($payload->message === 'new order') {
            $room = $this->users[$from->resourceId];
            foreach ($this->rooms[$room] as $client) {
                $client->send(json_encode($payload->value));
            }
        } elseif ($payload->message === 'new message') {
            $room = $this->users[$from->resourceId];
            foreach ($this->rooms[$room] as $client) {
                $message = ['message' => 'message', 'value' => $payload->value, 'user' => $this->users_name[$room][$from->resourceId]];
                $client->send(json_encode($message));
            }
        }

        foreach ($this->clients as $client) {
            // Закоменчено для демонстрации в одном браузере на разных страницах
            // if ($from != $client) {
            $client->send($msg);
            // }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        $room = $this->users[$conn->resourceId];
        unset($this->rooms[$room][$conn->resourceId]);
        unset($this->users[$conn->resourceId]);
        unset($this->users_name[$room][$conn->resourceId]);

        echo("connection $conn->resourceId  has disconnected\n");
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occured: {$e->getMessage()}\n";
        $conn->close();
    }

    private function notifyUsers(mixed $payload): void
    {
        $users = [];
        foreach ($this->users_name[$payload->value] as $user) {
            $users[] = $user;
        }
        $message = ['message' => 'connection', 'users' => $users];
        foreach ($this->rooms[$payload->value] as $client) {
            $client->send(json_encode($message));
        }
    }
}
