<?php

namespace Server;

use Server\Exception\SocketException;

class TcpSocketServer extends \Thread
{
    const LOOP_TIMEOUT = 200000;
    const READ_CHUNK = 1024;

    /** @var string */
    private $name;

    /** @var string */
    private $address;

    /** @var int */
    private $port;

    /** @var int */
    private $maxWaitingConnections;

    /** @var resource */
    private $socket;

    /** @var resource[] */
    protected $connections = [];



    /**
     * TcpSocketServer constructor.
     * @param $address
     * @param $port
     */
    public function __construct($name, $address, $port, $maxWaitingConnections = 10)
    {
        $this->name = $name;
        $this->address = $address;
        $this->port = $port;
        $this->maxWaitingConnections = $maxWaitingConnections;
    }


    protected function init()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (false === $this->socket ||
            false === socket_bind($this->socket, $this->address, $this->port) ||
            false === socket_set_nonblock($this->socket) ||
            false === socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1) ||
            false === socket_set_option($this->socket, SOL_SOCKET, SO_REUSEPORT, 1) ||
            false === socket_listen($this->socket, $this->maxWaitingConnections)
        ) {
            throw new SocketException($this->socket);
        }
    }

    public function run()
    {
        $this->init();
        $connections = [];
        $this->println("Started a server “{$this->name}” on {$this->address}::{$this->port}");
        $messageQueue = new \SplQueue();
        while (true) {
            $newConnection = socket_accept($this->socket);
            if ($newConnection !== false) {
                $connections[] = $newConnection;
                $this->println("Hey there, welcome to channel {$this->name}", $newConnection);
            }

            foreach ($connections as $index => $connectionSocket) {
                $connectionReading = socket_read($connectionSocket, self::READ_CHUNK);
                if (!empty($connectionReading)) {
                    $text = trim($connectionReading);
                    if ($text === 'bye') {
                        socket_close($connectionSocket);
                        unset($connections[$index]);
                        $text = "<left the conversation>";
                        $this->println("Removed a user ($index)");
                    }

                    $messageQueue->enqueue(new Message($text, $index));
                }
            }

            while (!$messageQueue->isEmpty()) {
                /** @var $msg Message */
                $msg = $messageQueue->dequeue();
                foreach ($connections as $index => $connectionSocket) {
                    if ($msg->getId() === $index) {
                        continue;
                    }
                    $this->println($msg->getMessageOutput(), $connectionSocket);
                }
            }
            usleep(self::LOOP_TIMEOUT);
        }
    }

    /**
     * @param $content
     */
    protected function println($content, $to = null)
    {
        $content = trim($content);
        $msg = "$content\r\n";
        if (is_resource($to)) {
            socket_write($to, $msg, strlen($msg));
        } else {
            echo $msg;
        }
    }
}