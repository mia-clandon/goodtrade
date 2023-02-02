<?php

namespace common\libs\form\traits;

use common\libs\form\components\Option;

/**
 * Class OptionsTrait
 * @package common\libs\form\traits
 * @author Артём Широких kowapssupport@gmail.com
 */
trait OptionsTrait {

    /** @var Option[] */
    private $options = [];

    /**
     * @param Option $option
     * @return $this
     */
    public function addOption(Option $option) {
        $this->options[$option->getOptionName()] = $option;
        return $this;
    }

    /**
     * @param Option[] $options
     * @return $this
     */
    public function setOptions(array $options) {
        foreach ($options as $option) {
            $this->addOption($option);
        }
        return $this;
    }

    /**
     * @param $option_name
     * @return Option|null
     */
    public function getOption($option_name) {
        return isset($this->options[$option_name]) ? $this->options[$option_name] : null;
    }

    /**
     * Возвращает значение первой опции.
     * @return mixed|null
     */
    public function getFirstOptionValue() {
        foreach ($this->options as $value => $title) {
            return $value;
        }
        return null;
    }

    /**
     * @return Option[]
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getOptionsAsArray() {
        $options = [];
        foreach ($this->getOptions() as $option) {
            $options = $options + $option->toArray();
        }
        return $options;
    }

    /**
     * Устанавливает массив опций.
     * @param array $options
     * @return $this
     */
    public function setArrayOfOptions(array $options) {
        foreach ($options as $value => $text) {
            $this->addOption(new Option($value, $text));
        }
        return $this;
    }
}