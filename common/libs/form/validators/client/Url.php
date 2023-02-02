<?php

namespace common\libs\form\validators\client;

/**
 * Валидатор Url
 * Class Url
 * @package common\libs\form\validators
 * @author Артём Широких kowapssupport@gmail.com
 */
class Url extends BaseValidator {

    /**
     * Url constructor.
     */
    public function __construct() {
        $this->addAttribute('data-validation', 'url');
    }
}