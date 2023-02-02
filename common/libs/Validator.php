<?php

namespace common\libs;

/**
 * Class Validator
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class Validator extends \Valitron\Validator {

    /** Язык сообщений. */
    const LANG = 'ru';

    /**
     * Validator constructor.
     * @param array $data
     * @param array $fields
     * @param null|string $lang
     * @param null|string $langDir
     */
    public function __construct(array $data, array $fields = [], $lang = null, $langDir = null) {
        parent::__construct($data, $fields, self::LANG, $langDir);
    }

    protected function validateEquals($field, $value, array $params) {
        $field2 = $params[0];
        return isset($this->_fields[$field]) && $value == $field2;
    }
}