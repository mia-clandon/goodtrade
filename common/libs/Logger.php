<?php

namespace common\libs;

use Katzgrau\KLogger\Logger as KLogger;

/**
 * Class Logger
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class Logger {

    /** @var  KLogger */
    protected static $instance;

    final public static function getInstance() {
        if (!isset(static::$instance)) {
            $logger = new KLogger(\Yii::getAlias('@runtime/logs'));
            static::$instance = $logger;
        }
        return static::$instance;
    }

    /**
     * alias getInstance
     * @return KLogger
     */
    final public static function get() {
        return static::getInstance();
    }

    final private function __construct() {
        $this->init();
    }

    protected function init() {}
    final private function __clone() {}
}