<?php

namespace common\libs;

use yii\helpers\StringHelper as Helper;

/**
 * Class StringHelper
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class StringHelper extends Helper {

    /**
     * @return string
     */
    public static function join() {
        $args = (array)func_get_args();
        return implode('', $args);
    }

    /**
     * Метод проверяет, является ли переданная строка JSON'ом.
     * @param $string
     * @return bool
     */
    public static function isJson($string) {
        if (!is_string($string)) {
            return false;
        }
        return is_array(json_decode($string, true));
    }

    /**
     * Преобразовывает слово с заглавной буквы.
     * @param string $word
     * @return string
     */
    public static function firstUpperLetter($word) {
        $first_letter = mb_strtolower(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
        $last = mb_substr($word, 1);
        return $first_letter. $last;
    }

    /**
     * Обрезает строку до $count_symbols.
     * @param string $string
     * @param int $count_symbols
     * @return string
     */
    public static function cutString(string $string, int $count_symbols): string {
        return mb_substr($string, 0, $count_symbols);
    }
}