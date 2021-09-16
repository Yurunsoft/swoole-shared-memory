<?php

declare(strict_types=1);

namespace Yurun\Swoole\SharedMemory\Client\Store;

use Yurun\Swoole\SharedMemory\Interfaces\IKV;
use Yurun\Swoole\SharedMemory\Message\Operation;

class KV extends Base implements IKV
{
    /**
     * 写入值
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function set($name, $value)
    {
        return $this->doCall(new Operation('KV', 'set', [$name, $value]));
    }

    /**
     * 获取值
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->doCall(new Operation('KV', 'get', [$name, $default]));
    }

    /**
     * 移除值
     *
     * @param string $name
     *
     * @return bool
     */
    public function remove($name)
    {
        return $this->doCall(new Operation('KV', 'remove', [$name]));
    }

    /**
     * 是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public function exists($name)
    {
        return $this->doCall(new Operation('KV', 'exists', [$name]));
    }

    /**
     * 清除.
     *
     * @return bool
     */
    public function clear()
    {
        return $this->doCall(new Operation('KV', 'clear'));
    }

    /**
     * 获取总的存储数据条数.
     *
     * @return int
     */
    public function count()
    {
        return $this->doCall(new Operation('KV', 'count'));
    }
}
