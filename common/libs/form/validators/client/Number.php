<?php

namespace common\libs\form\validators\client;

/**
 * Валидатор чисел
 * Class Number
 * @package common\libs\form\validators
 * @author Артём Широких kowapssupport@gmail.com
 */
class Number extends BaseValidator {

    /**
     * Number constructor.
     * Any numerical value
     */
    public function __construct() {
        $this->addAttribute('data-validation', 'number');
    }

    /**
     * Only allowing float values
     * @return $this
     */
    public function allowFloat() {
        $this->addAttribute('data-validation-allowing', 'float');
        return $this;
    }

    /**
     * Allowing float values and negative values
     * @return $this
     */
    public function allowNegative() {
        $this->addAttribute('data-validation-allowing', 'negative');
        return $this;
    }

    /**
     * Validate float number with comma separated decimals
     * @param string $separator
     * @return $this
     */
    public function setDecimalSeparator($separator) {
        $this->addAttribute('data-validation-decimal-separator', $separator);
        return $this;
    }

    /**
     * Only allowing numbers from $from to $to
     * @param float $from
     * @param float $to
     * @return $this
     */
    public function rangeValidator($from, $to) {
        $this->addAttribute('data-validation-allowing', 'range['.implode(';',[$from, $to]).']');
        return $this;
    }
}