<?php

/**
 * This file is part of FastPay.
 *
 * Copyright (c) 2014 Yahoo Japan Corporation
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FastPay;

class FastPayObject implements \ArrayAccess
{
    protected $values;

    public function __construct(array $values = array())
    {
        unset($values["object"]);
        $this->values = $values;
    }

    public function __get($key)
    {
        return isset($this->values[$key]) ? $this->values[$key] : null;
    }

    public function __set($key, $value)
    {
        $this->values[$key] = $value;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->values[$offset]) ? $this->values[$offset] : null;
    }

    public function getArray()
    {
        return $this->values;
    }
}
