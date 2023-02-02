<?php

namespace common\libs;

/**
 * Class Declension
 * Класс для работы со склонениями слов.
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class Declension {

    /**
     * Склонение числительных.
     * @param integer $number
     * @param string $for_one
     * @param string $for_lt_four
     * @param string $for_gt_five
     * @param bool $with_number
     * @return string
     */
    public static function number($number, $for_one, $for_lt_four, $for_gt_five, $with_number = false) {
        $number = abs($number) % 100;
        if ($number >= 11 && $number <= 19) {
            $result = $for_gt_five;
        }
        else {
            $i = $number % 10;
            switch ($i) {
                case (1): $result = $for_one; break;
                case (2):
                case (3):
                case (4): $result = $for_lt_four; break;
                default: $result = $for_gt_five;
            }
        }
        if ($with_number) {
            $result = $number. ' '.$result;
        }
        return $result;
    }

}