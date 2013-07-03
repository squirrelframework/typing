<?php 

namespace Squirrel\Typing;

/**
 * Main class for all objects, providing some global functionnalities.
 *
 * @package Squirrel\Typing
 * @author Valérian Galliat
 */
abstract class Object
{
    /**
     * Instanciates a new object with given arguments.
     *
     * @return Squirrel\Object new instance
     */
    public static function create()
    {
        // Reflect current class
        $reflection = new \ReflectionClass(get_called_class());

        // Make a new instance
        return $reflection->newInstanceArgs(func_get_args());
    }

    /**
     * Gets a singleton instance of current class.
     *
     * If one argument if given, it will be assumed as the
     * instance's name and will be passed to constructor.
     *
     * If multiple arguments are given, first argument
     * will be assumed as the instance's name but will not
     * be passed to constructor.
     *
     * @return Squirrel\Object singleton instance
     */
    public static function instance()
    {
        static $instance  = null;
        static $instances = array();

        // Get arguments array
        $arguments = func_get_args();

        if (empty($arguments))
        {
            if ($instance !== null)
            {
                // Base instance already exists
                return $instance;
            }

            // Create new instance and return it
            return $instance = static::factory();
        }

        // Get instance name
        $name = array_shift($arguments);

        if (isset($instances[$name]))
        {
            // Named instance already exists
            return $instances[$name];
        }

        // Get factory callback
        $callback = Callback::cast(array(get_called_class(), 'factory'));

        // Create named instance and return it
        return $instances[$name] = $callback->apply(
            empty($arguments) ? array($name) : $arguments);
    }

    /**
     * Casts given argument as a current class instance.
     *
     * Class constructor must be compatible with
     * this kind of instanciation.
     *
     * @param  mixed           argument
     * @return Squirrel\Object casted argument
     */
    public static function cast($argument)
    {
        if ($argument instanceof static)
        {
            // Argument is already casted
            return $argument;
        }

        // Return casted argument
        return static::factory($argument);
    }

    /**
     * Returns whether given class is child of called class.
     *
     * @param  string class name
     * @return bool
     */
    public static function isChild($name)
    {
        return class_exists($name) &&
               in_array(get_called_class(), class_parents($name));
    }

    /**
     * Returns whether instance has given method or not.
     *
     * @param  string method name
     * @return bool
     */
    public function hasMethod($name)
    {
        return method_exists($this, $name);
    }

    /**
     * Gets a callback for given method.
     *
     * @param  string method name
     * @return Callback
     */
    public function callback($name)
    {
        return Callback::cast(array($this, $name));
    }

    /**
     * Returns whether instance equals given param
     * regarding of their properties.
     *
     * This method can be overriden in children classes
     * to provide a more accurate behavior.
     *
     * @param  mixed param
     * @return bool
     */
    public function equals($param)
    {
        return $this == $param;
    }

    /**
     * Dumps object for debug, can be overriden
     * for better display.
     *
     * @param  bool return
     * @return void|string
     */
    public function dump($return = false)
    {
        if ($return)
        {
            ob_start();
        }

        var_dump($this);

        if ($return)
        {
            return ob_get_clean();
        }
    }
}
