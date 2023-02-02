<?php

namespace common\libs\form\validators\client;

/**
 * Валидатор Email
 * Class Email
 * @package common\libs\form\validators
 * @author Артём Широких kowapssupport@gmail.com
 */
class Email extends BaseValidator {

    /**
     * Email constructor.
     */
    public function __construct() {
        $this->addAttribute('data-validation', 'email');
    }
}