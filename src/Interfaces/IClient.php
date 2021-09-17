<?php

declare(strict_types=1);

namespace Yurun\Swoole\SharedMemory\Interfaces;

use Yurun\Swoole\SharedMemory\Message\Operation;

interface IClient
{
    /**
     * 构造方法.
     *
     * @param array $options 配置
     */
    public function __construct($options = []);

    /**
     * 连接.
     */
    public function connect(): bool;

    /**
     * 发送操作.
     */
    public function send(Operation $operation): bool;

    /**
     * 接收结果.
     *
     * @return \Yurun\Swoole\SharedMemory\Message\Result|bool
     */
    public function recv();
}
