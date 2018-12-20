<?php
namespace Yurun\Swoole\SharedMemory\Client;

use Yurun\Swoole\SharedMemory\Message\Result;
use Yurun\Swoole\SharedMemory\Message\Operation;
use Yurun\Swoole\SharedMemory\Interfaces\IClient;

class SwooleClient implements IClient
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
     * Swoole 协程客户端
     *
     * @var \Swoole\Coroutine\Client
     */
    private $socket;

    /**
     * 是否已连接
     *
     * @var boolean
     */
    private $connected;

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
        if($this->connected)
        {
            return true;
        }
        $this->socket = new \Swoole\Coroutine\Client(SWOOLE_SOCK_UNIX_STREAM);
        $this->socket->set([
            'open_tcp_nodelay'      =>  false,
            'open_length_check'     => 1,
            'package_length_type'   => 'N',
            'package_length_offset' => 0,       //第N个字节是包长度的值
            'package_body_offset'   => 4,       //第几个字节开始计算长度
            'package_max_length'    => 2 * 1024 * 1024,  //协议最大长度，默认2M
        ]);
        if(false === $this->socket->connect($this->socketFile, 0))
        {
            $this->connected = false;
            return false;
        }
        $this->connected = true;
        return true;
    }

    /**
     * 关闭连接
     *
     * @return void
     */
    public function close()
    {
        if($this->connected)
        {
            $this->socket->close();
            $this->socket = null;
        }
    }

    /**
     * 是否已连接
     *
     * @return boolean
     */
    public function isConnected(): bool
    {
        return $this->connected;
    }

    /**
     * 发送操作
     *
     * @param \Yurun\Swoole\SharedMemory\Message\Operation $operation
     * @return boolean
     */
    public function send(Operation $operation): bool
    {
        if(!$this->connected || !$this->connect())
        {
            return false;
        }
        $data = ($this->serialize)($operation);
        $length = strlen($data);
        $data = pack('N', $length) . $data;
        $length += 4;
        $result = $this->socket->send($data);
        if(false === $result)
        {
            $this->close();
        }
        return $length === $result;
    }

    /**
     * 接收结果
     *
     * @return \Yurun\Swoole\SharedMemory\Message\Result|boolean
     */
    public function recv()
    {
        if(!$this->connected || !$this->connect())
        {
            return false;
        }
        $data = $this->socket->recv();
        if(false === $data)
        {
            $this->close();
            return false;
        }
        $result = ($this->unserialize)(substr($data, 4));
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