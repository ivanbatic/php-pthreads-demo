<?php
use Server\TcpSocketServer;

include __DIR__ . '/../vendor/autoload.php';

$address = isset($argv[1]) ? $argv[1] : 'localhost';
$ports = array_slice($argv, 2);
$msg = new \Server\Message('Hello', 0);
/** @var TcpSocketServer[] $servers */
$servers = new SplFixedArray(count($ports));

foreach ($ports as $portIndex => $port) {
    $server = new TcpSocketServer("Server $portIndex", $address, $port);
    $servers[$portIndex] = $server;
    $server->start();
}