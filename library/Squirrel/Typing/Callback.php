<?php

namespace Squirrel\Typing;

/**
 * Provides an interface for calling
 * functions or methods.
 *
 * @package Squirrel\Typing
 * @author  Valérian
 */
class Callback extends Object
{
    /**
     * @var string|object
     */
    protected $class;

    /**
     * @var string
     */
    protected $function;

    /**
     * Initializes given callback.
     *
     * If first argument is an array, it will be assumed
     * as a PHP callback having object in first offset
     * and method name in second offset. If the callback
     * is not valid, an exception will be thrown.
     *
     * If first argument is a string it will be assumed
     * as a function name, but if there is a second argument
     * it will be assumed as a static class name and
     * the second argument will be considered
     * as a static method name.
     *
     * Else, first argument must be a class instance
     * and second argument the member method name to call,
     * or if not specified, instance must be invokable.
     *
     * @throws \InvalidArgumentException
     * @param mixed
     * @param string
     */
    public function __construct($class, $function = null)
    {
        if (is_array($class))
        {
            if (count($class) !== 2)
            {
                throw new Exception('Given PHP callback is not valid, '
                                  . 'must have two items in array');
            }

            if (!is_string($class[0]) && !is_object($class[0]))
            {
                throw new Exception('Given PHP callback is not valid, '
                                  . 'first item must be whether a string '
                                  . 'or an object');
            }

            if (!is_string($class[1]))
            {
                throw new Exception('Given PHP callback is not valid, '
                                  . 'second item must be a string');
            }

            $this->class    = $class[0];
            $this->function = $class[1];

            return;
        }

        if (!isset($function))
        {
            $this->function = $class;
            return;
        }

        $this->class    = $class;
        $this->function = $function;
    }

    /**
     * Invokes callback with given arguments.
     *
     * @return mixed
     */
    public function __invoke()
    {
        return $this->apply(func_get_args());
    }

    /**
     * Sets callback class.
     *
     * @param mixed
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Gets callback class.
     *
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Sets callback function.
     *
     * @param string
     */
    public function setFunction($function)
    {
        $this->function = $function;
    }

    /**
     * Gets callback function.
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Returns whether callback is callable or not.
     *
     * @return bool
     */
    public function isCallable()
    {
        return is_callable($this->compile());
    }

    /**
     * Returns whether callback is a class method or not.
     *
     * @return bool
     */
    public function isMethod()
    {
        if (!isset($this->class))
        {
            return false;
        }

        return method_exists($this->class, $this->function);
    }

    /**
     * Invokes callback with given arguments.
     *
     * @return mixed
     */
    public function call()
    {
        return $this->apply(func_get_args());
    }

    /**
     * Invokes callback with given arguments array.
     *
     * @throws Squirrel\Exception
     * @param  array
     * @return mixed
     */
    public function apply(array $arguments = null)
    {
        if (!$this->isCallable())
        {
            throw new Exception('Callback is not valid');
        }

        if ($arguments === null)
        {
            // Invoke callback without arguments
            return call_user_func($this->compile());
        }

        // Invoke callback with arguments array
        return call_user_func_array($this->compile(), $arguments);
    }

    /**
     * Compiles callback to PHP format.
     *
     * @return string|array
     */
    public function compile()
    {
        if (!isset($this->class))
        {
            return $this->function;
        }

        if (is_string($this->class))
        {
            return $this->class . '::' . $this->function;
        }

        return array($this->class, $this->function);
    }
}
