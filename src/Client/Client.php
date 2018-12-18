<?php
namespace Yurun\Swoole\SharedMemory\Client;

use Yurun\Swoole\SharedMemory\Message\Result;
use Yurun\Swoole\SharedMemory\Message\Operation;

class Client
{
    /**
     * socket 文件路径
     * 
     * 不支持 samba 文件共享
     *
     * @var string
     */
    private $socketFile;

    /**
     * 序列化方法
     *
     * @var callable
     */
    private $serialize;

    /**
     * 反序列化方法
     *
     * @var callable
     */
    private $unserialize;

    /**
     * socket 资源
     *
     * @var resource
     */
    private $socket;

    /**
     * 构造方法
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $options;
        if(!isset($this->options['socketFile']))
        {
            throw new \InvalidArgumentException('If you want to use Swoole Shared Memory, you must set the "socketFile" option');
        }
        $this->socketFile = $this->options['socketFile'];
        $this->serialize = $this->options['serialize'] ?? 'serialize';
        $this->unserialize = $this->options['unserialize'] ?? 'unserialize';
    }

    /**
     * 连接
     *
     * @return boolean
     */
    public function connect(): bool
    {
        $this->socket = stream_socket_client('unix://' . $this->socketFile, $errno, $errstr);
        if(false === $this->socket)
        {
            return false;
        }
        return true;
    }

    /**
     * 发送操作
     *
     * @param \Yurun\Swoole\SharedMemory\Message\Operation $operation
     * @return boolean
     */
    public function send(Operation $operation): bool
    {
        $data = ($this->serialize)($operation);
        $length = strlen($data);
        $data = pack('N', $length) . $data;
        $length += 4;
        return $length === fwrite($this->socket, $data, $length);
    }

    /**
     * 接收结果
     *
     * @return \Yurun\Swoole\SharedMemory\Message\Result|boolean
     */
    public function recv()
    {
        $meta = fread($this->socket, 4);
        if('' === $meta || false === $meta)
        {
            return false;
        }
        $length = unpack('N', $meta)[1];
        $data = fread($this->socket, $length);
        if(false === $data || !isset($data[$length - 1]))
        {
            return false;
        }
        $result = ($this->unserialize)($data);
        if($result instanceof Result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }
}