<?php

namespace Squirrel\Typing;

/**
 * Object wrapper for array functions.
 *
 * @package Squirrel\Typing
 * @author  Valérian
 */
class Collection extends Object implements \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * @var array
     */
    protected $array;

    /**
     * @param array $array
     */
    public function __construct(array $array = array())
    {
        $this->array = $array;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->array);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->array[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->array[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->array);
    }

    /**
     * Returns a JSON version of array.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->array);
    }

    /**
     * Gets native PHP array.
     *
     * @return array
     */
    public function asArray()
    {
        return $this->array;
    }

    /**
     * Returns whether array is empty.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->count() === 0;
    }

    /**
     * Empties array.
     *
     * @return void
     */
    public function clear()
    {
        empty($this->array);
    }

    /**
     * Returns whether array has given key.
     *
     * @param mixed key
     * @return boolean
     */
    public function has($key)
    {
        return isset($this[$key]);
    }

    /**
     * Sets given key and value pair in array.
     *
     * @param mixed key
     * @param mixed value
     * @return Collection
     */
    public function set($key, $value)
    {
        $this[$key] = $value;
        return $this;
    }

    /**
     * Gets given array item.
     *
     * @param mixed key
     * @param mixed fallback value
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this[$key]) ? $this[$key] : $default;
    }

    /**
     * Uses arguments as path to find value.
     *
     * @return mixed
     */
    public function find()
    {
        $parts  = Collection::cast(func_get_args());
        $result = $this->array;

        while (!$parts->isEmpty())
        {
            if (!is_array($result) || !isset($result[$parts[0]]))
            {
                return null;
            }

            $result = $result[$parts->shift()];
        }

        return $result;
    }

    /**
     * Finds in array using dot separator.
     *
     * @param string path
     * @param mixed  default value
     * @return mixed
     */
    public function path($path, $default = null)
    {
        $parts  = String::cast($path)->split('.');
        $result = Callback::cast(array($this, 'find'))->apply($parts);
    }

    /**
     * Simplier slice method.
     *
     * @param int start
     * @param int length
     * @return Collection
     */
    public function cut($start, $length = null)
    {
        if ($length === null)
        {
            return $this->slice($start);
        }

        return $this->slice($start, $length);
    }

    /**
     * @see in_array
     */
    public function contains($value)
    {
        return in_array($value, $this->array, true);
    }

    /**
     * @see array_keys
     */
    public function keys()
    {
        return new static(array_keys($this->array));
    }

    /**
     * @see array_values
     */
    public function values()
    {
        return new static(array_values($this->array));
    }


    /**
     * @see array_push
     */
    public function push($value)
    {
        return array_push($this->array, $value);
    }

    /**
     * @see array_pop
     */
    public function pop()
    {
        return array_pop($this->array);
    }

    /**
     * @see array_unshift
     */
    public function unshift($value)
    {
        return array_unshift($this->array, $value);
    }

    /**
     * @see array_shift
     */
    public function shift()
    {
        return array_shift($this->array);
    }

    /**
     * @see array_flip
     */
    public function flip()
    {
        return new static(array_flip($this->array));
    }

    /**
     * @see array_reverse
     */
    public function reverse()
    {
        return new static(array_reverse($this->array));
    }

    /**
     * @see array_filter
     */
    public function filter($callback)
    {
        return new static(array_filter($this->array, $callback));
    }

    /**
     * @see array_slice
     */
    public function slice($offset, $length = null, $preserve = false)
    {
        $array = array_slice($this->array, $offset, $length, $preserve);
        return new static($array);
    }

    /**
     * @see array_splice
     */
    public function splice($offset, $length = 0, $replacement = null)
    {
        if (func_num_args() < 2)
        {
            $array = array_splice($this->array, $offset);
            return new static($array);
        }

        if (func_num_args() < 3)
        {
            $array = array_splice($this->array, $offset, $length);
            return new static($array);
        }

        $array = array_splice($this->array, $offset, $length, $replacement);
        return new static($array);
    }

    /**
     * @see implode
     */
    public function join($glue)
    {
        return new String(implode($glue, $this->array));
    }

    /**
     * @see sort
     */
    public function sort($flags = SORT_REGULAR)
    {
        return sort($this->array, $flags);
    }
}
