<?php

declare(strict_types=1);
require dirname(__DIR__) . '/vendor/autoload.php';

use Yurun\Swoole\SharedMemory\Server;

$options = [
    // 这个文件必须，而且不能是samba共享文件
    'socketFile'    => '/tmp/swoole-shared-memory.sock',
    'storeTypes'    => [
        \Yurun\Swoole\SharedMemory\Store\KV::class,
        \Yurun\Swoole\SharedMemory\Store\Stack::class,
        \Yurun\Swoole\SharedMemory\Store\Queue::class,
        \Yurun\Swoole\SharedMemory\Store\PriorityQueue::class,
    ],
];
$server = new Server($options);
$server->run();
