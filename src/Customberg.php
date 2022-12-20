<?php

namespace Customberg\PHP;

use Customberg\PHP\CustombergInstance;
use Spatie\Macroable\Macroable;

class Customberg
{
    use Macroable;

    protected static $instance;

    public static function getInstance(): CustombergInstance
    {
        if (!isset(static::$instance)) {
            static::$instance = new CustombergInstance();
        }
        return static::$instance;
    }

    public static function __callStatic($method, $args = [])
    {
        if (substr($method, 0, 1) == '$') {
            $method = substr($method, 1);
        }
        if ($method == 'instance') {
            return static::$instance;
        }
        return call_user_func_array([static::getInstance(), $method], $args);
    }
}
