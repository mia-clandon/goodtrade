<?php

namespace common\libs\form\validators\client;

use common\libs\form\traits\AttributesTrait;

/**
 * Class BaseValidator
 * Клиенский валидатор, работает с http://formvalidator.net/ (BaseValidator.php)
 * @package common\libs\form\validators
 * @author Артём Широких kowapssupport@gmail.com
 */
class BaseValidator {

    use AttributesTrait;

    const RULES_SEPARATOR = ',';

    /**
     * Устанавливает текст ошибки для клиенской валидации.
     * @param $error_message
     * @return $this
     */
    public function setErrorMessage($error_message) {
        if (!empty($error_message)) {
            $class_name = join('', array_slice(explode('\\', get_class($this)), -1));
            $class_name = strtolower($class_name);
            $this->addAttribute('data-validation-error-msg-'. $class_name, $error_message);
        }
        return $this;
    }

    /**
     * Валидатор добавляет к существующему атрибуту новое правило через запятую
     * @param $name
     * @param $value
     * @return $this
     */
    public function addAttribute($name, $value) {
        $attributeValue = $this->getAttribute($name);
        if (!is_null($attributeValue)) {
            $attributeValue.= self::RULES_SEPARATOR. $value;
            $this->attributes[$name] = $attributeValue;
        }
        else {
            $this->attributes[$name] = $value;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributeRules() {
        return $this->getAttributes();
    }
}