<?php
namespace Yurun\Swoole\SharedMemory\Store;

use Yurun\Swoole\SharedMemory\Interfaces\IQueue;

class Queue implements IQueue
{
    /**
     * 存储的数据
     *
     * @var array
     */
    private $data = [];

    /**
     * 获取队列对象
     *
     * @param string $name
     * @return \SplQueue
     */
    private function getQueue($name)
    {
        if(!isset($this->data[$name]))
        {
            $this->data[$name] = new \SplQueue;
        }
        return $this->data[$name];
    }

    /**
     * 队列是否为空
     *
     * @param string $name
     * @return boolean
     */
    public function empty($name)
    {
        return $this->getQueue($name)->isEmpty();
    }

    /**
     * 弹出队列首个元素
     *
     * @param string $name
     * @return mixed|boolean
     */
    public function pop($name)
    {
        $queue = $this->getQueue($name);
        return $queue->isEmpty() ? false : $queue->dequeue();
    }

    /**
     * 在队列尾部增加元素
     *
     * @param string $name
     * @param mixed $element
     * @return boolean
     */
    public function push($name, ...$element)
    {
        $result = true;
        foreach($element as $e)
        {
            $result &= $this->getQueue($name)->enqueue($e);
        }
        return $result;
    }

    /**
     * 返回队列长度
     *
     * @param string $name
     * @return int
     */
    public function size($name)
    {
        return $this->getQueue($name)->count();
    }

    /**
     * 返回队首元素
     *
     * @param string $name
     * @return mixed
     */
    public function front($name)
    {
        $queue = $this->getQueue($name);
        return $queue->isEmpty() ? false : $queue->top();
    }

    /**
     * 返回队尾元素
     *
     * @param string $name
     * @return void
     */
    public function back($name)
    {
        $queue = $this->getQueue($name);
        return $queue->isEmpty() ? false : $queue->bottom();
    }

    /**
     * 清空队列
     *
     * @param string $name
     * @return void
     */
    public function clear($name)
    {
        $this->data[$name] = new \SplQueue;
    }

}