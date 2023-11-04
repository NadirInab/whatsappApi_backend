<?php

namespace App\Websocket;

use SplObjectStorage;
use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class MessageHandler implements MessageComponentInterface
{

    protected $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo "New connection! ({$conn->resourceId})\n";
        $this->connections->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $data)
    {
        $numRecv = count($this->connections) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other            connection%s' . "\n", $from->resourceId, $data, $numRecv, $numRecv ==  1 ? '' : 's');
        foreach ($this->connections as $connection) {
            if ($connection === $from) {
                continue;
            }
            $connection->send($data);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->connections->detach($conn);
        $conn->close();
    }

    public function sendToClients($data)
    {
        echo "Helo form message handler " ;
        foreach ($this->connections as $connection) {
            $connection->send(json_encode($data));
        }
    }
}
