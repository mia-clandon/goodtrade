<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace common\libs\traits;

/**
 * Одиночка
 * Class Singleton
 * @author Артём Широких kowapssupport@gmail.com
 */
trait Singleton
{
    protected static $instance;

    final public static function getInstance(): static
    {
        return static::$instance ?? static::$instance = new static;
    }

    final public static function i(): static
    {
        return static::getInstance();
    }

    private function __construct()
    {
        $this->init();
    }

    protected function init()
    {
    }

    private function __clone()
    {
    }
}