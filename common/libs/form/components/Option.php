<?php

namespace common\libs\form\components;

/**
 * Опция Select'a
 * Class Option
 * @package common\libs\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class Option extends Control {

    /** @var string */
    private $name;
    /** @var string */
    protected $value;

    /**
     * Option constructor.
     * @param string $name
     * @param string $value
     */
    public function __construct($name='', $value='') {
        $this->add($name, $value);
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return Option
     */
    public function add($name, $value) {
        $this->name = $name;
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getOptionValue() {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getOptionName() {
        return $this->name;
    }

    /**
     * @return array
     */
    public function toArray() {
        return [$this->getOptionName() => $this->getOptionValue()];
    }
}