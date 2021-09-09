<?php

namespace app\components;

use Psr\SimpleCache\CacheInterface;

class CacheFake implements CacheInterface
{

    public function get($key, $default = null)
    {
        return null;
    }

    public function set($key, $value, $ttl = null)
    {
        return false;
    }

    public function delete($key)
    {
        // TODO: Implement delete() method.
    }

    public function clear()
    {
        // TODO: Implement clear() method.
    }

    public function getMultiple($keys, $default = null)
    {
        // TODO: Implement getMultiple() method.
    }

    public function setMultiple($values, $ttl = null)
    {
        // TODO: Implement setMultiple() method.
    }

    public function deleteMultiple($keys)
    {
        // TODO: Implement deleteMultiple() method.
    }

    public function has($key)
    {
        return false;
    }
}