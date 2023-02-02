<?php

namespace common\libs\form\validators\client;

/**
 * Валидатор Regexp
 * Class Regexp
 * @package common\libs\form\validators
 * @author Артём Широких kowapssupport@gmail.com
 */
class Regexp extends BaseValidator {

    /**
     * Regexp constructor.
     */
    public function __construct() {
        $this->addAttribute('data-validation', 'custom');
    }

    /**
     * This input would only allow $rule
     * @param $rule
     * @return $this
     */
    public function setRegexp($rule) {
        $this->addAttribute('data-validation-regexp', $rule);
        return $this;
    }
}