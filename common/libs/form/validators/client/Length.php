<?php

namespace common\libs\form\validators\client;

/**
 * Валидатор на длину текста
 * Class Length
 * @package common\libs\form\validators
 * @author Артём Широких kowapssupport@gmail.com
 */
class Length extends BaseValidator {

    /**
     * Length constructor.
     */
    public function __construct() {
        $this->addAttribute('data-validation', 'length');
        return $this;
    }

    /**
     * Максимальное количество символов.
     * @param int $max
     * @return $this
     */
    public function addMax($max) {
        $this->addAttribute('data-validation-length', 'max'. $max);
        return $this;
    }

    /**
     * Минимальное количество символов.
     * @param int $min
     * @return $this
     */
    public function addMin($min) {
        $this->addAttribute('data-validation-length', 'min'. $min);
        return $this;
    }

    /**
     * No less than $min characters and no more than $max characters
     * @param $min
     * @param $max
     * @return $this
     */
    public function addRange($min, $max) {
        $this->addAttribute('data-validation-length', implode('-', [$min, $max]));
        return $this;
    }
}