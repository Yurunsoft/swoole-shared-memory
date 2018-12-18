<?php
namespace Yurun\Swoole\SharedMemory\Store;

use Yurun\Swoole\SharedMemory\Interfaces\IStack;

class Stack implements IStack
{
    /**
     * 存储的数据
     *
     * @var array
     */
    private $data = [];

    /**
     * 获取栈对象
     *
     * @param string $name
     * @return \SplStack
     */
    private function getStack($name)
    {
        if(!isset($this->data[$name]))
        {
            $this->data[$name] = new \SplStack;
        }
        return $this->data[$name];
    }

    /**
     * 栈是否为空
     *
     * @param string $name
     * @return boolean
     */
    public function empty($name)
    {
        return $this->getStack($name)->isEmpty();
    }

    /**
     * 弹出栈顶元素
     *
     * @param string $name
     * @return mixed|boolean
     */
    public function pop($name)
    {
        $stack = $this->getStack($name);
        return $stack->isEmpty() ? false : $stack->pop();
    }

    /**
     * 在栈底增加元素
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
            $result &= $this->getStack($name)->push($e);
        }
        return $result;
    }

    /**
     * 返回栈中元素数目
     *
     * @param string $name
     * @return int
     */
    public function size($name)
    {
        return $this->getStack($name)->count();
    }

    /**
     * 返回栈顶元素
     *
     * @param string $name
     * @return mixed
     */
    public function top($name)
    {
        $stack = $this->getStack($name);
        return $stack->isEmpty() ? false : $stack->top();
    }

    /**
     * 清空栈
     *
     * @param string $name
     * @return void
     */
    public function clear($name)
    {
        $this->data[$name] = new \SplStack;
    }

}