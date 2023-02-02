<?php

namespace common\libs\form\validators\client;

/**
 * Валидатор дат.
 * Class Date
 * @package common\libs\form\validators
 * @author Артём Широких kowapssupport@gmail.com
 */
class Date extends BaseValidator {

    /**
     * Date constructor.
     * Validate date formatted yyyy-mm-dd
     */
    public function __construct() {
        $this->addAttribute('data-validation', 'date');
    }

    /**
     * Validate date formatted yyyy-mm-dd but dont require leading zeros
     * @return $this
     */
    public function setRequireLeadingZero() {
        $this->addAttribute('data-validation-require-leading-zero', 'false');
        return $this;
    }

    /**
     * Validate date formatted dd.mm.yyyy
     * @param string $format
     * @return $this
     */
    public function setDateFormat($format = 'dd.mm.yyyy') {
        $this->addAttribute('data-validation-format', $format);
        return $this;
    }
}