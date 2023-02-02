<?php

namespace common\libs;

/**
 * Class StringBuilder
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class StringBuilder {

    /** @var array */
    private $string_parts = [];

    /** @var string Разделитель после каждого вызова add. */
    private $comma = '';

    /**
     * Добавление разделителя после вызова add.
     * @param string $comma
     * @return $this
     */
    public function setComma($comma = null) {
        if (!is_null($comma)) {
            $this->comma = (string)$comma;
        }
        return $this;
    }

    /**
     * @param string $part
     * @param bool $use_comma
     * @return $this
     */
    public function add($part, $use_comma = true) {
        if (!empty($part)) {
            $this->string_parts[] = (string)$part;
            // разделитель
            if (!empty($this->comma) && $use_comma) {
                $this->string_parts[] = $this->comma;
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function get() {
        return (string)implode('', $this->string_parts);
    }
}