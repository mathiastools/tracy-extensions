<?php 
namespace Mathiastools\TracyExtensions\Profiler;

/**
 * Singleton trait
 *
 * @author   Matej Erdős
 * @author   Petr Knap <dev@petrknap.cz>
 * @license  https://github.com/petrknap/php-singleton/blob/master/LICENSE MIT
 */
trait SingletonTrait
{
    /**
     * @var self[]
     */
    private static $instances = [];
    
    /**
     * Returns instance, if instance does not exist then creates new one and returns it
     *
     * @return $this
     */
    public static function getInstance()
    {
        $self = static::class;
        if (!isset(self::$instances[$self])) {
            self::$instances[$self] = new $self;
        }
        return self::$instances[$self];
    }
    
    /**
     * @return bool true if has instance, otherwise false
     */
    public static function hasInstance()
    {
        $self = static::class;
        return isset(self::$instances[$self]);
    }
}