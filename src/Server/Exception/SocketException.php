<?php

namespace Server\Exception;
class SocketException extends \Exception
{
    protected $socket;

    public function __construct($socket = null)
    {
        $this->socket = $socket;
        $errorCode = socket_last_error($this->socket);
        $errorStr = socket_strerror($errorCode);
        parent::__construct($errorStr, $errorCode);
    }
}