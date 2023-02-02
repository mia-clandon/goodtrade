<?php

namespace common\libs\form\validators\client;

/**
 * Обязательно для заполнения
 * Class Required
 * @package common\libs\form\validators
 * @author Артём Широких kowapssupport@gmail.com
 */
class Required extends BaseValidator {

    /**
     * Required constructor.
     */
    public function __construct() {
        $this->addAttribute('data-validation', 'required');
    }
}