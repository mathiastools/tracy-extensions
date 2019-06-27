<?php 
namespace Mathiastools\TracyExtensions\Profiler;

/**
 * Singleton interface
 *
 * @author   Matej Erdős
 * @author   Petr Knap <dev@petrknap.cz>
 * @license  https://github.com/petrknap/php-singleton/blob/master/LICENSE MIT
 */
interface SingletonInterface
{
    /**
     * Returns instance, if instance does not exist then creates new one and returns it
     *
     * @return $this
     */
    public static function getInstance();
}