<?php

namespace common\libs\form\validators\client;

/**
 * Валидатор Alphanumeric
 * Class Alphanumeric
 * @package common\libs\form\validators
 * @author Артём Широких kowapssupport@gmail.com
 */
class Alphanumeric extends BaseValidator {

    /**
     * Alphanumeric constructor.
     */
    public function __construct() {
        $this->addAttribute('data-validation', 'alphanumeric');
    }

    /**
     * Input requires the same as the one above but it also allows hyphen and underscore
     * @param $rules
     * @return $this
     */
    public function addAllowing($rules) {
        $this->addAttribute('data-validation-allowing', $rules);
        return $this;
    }
}