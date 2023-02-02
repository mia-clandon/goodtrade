<?php

namespace common\libs;

use common\libs\traits\Singleton;

/**
 * Class DaysAgo
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class DaysAgo {

    use Singleton;

    const DAYS = 1;
    const MONTHS = 2;
    const YEARS = 3;

    /**
     * @param int $timestamp
     * @return string
     */
    public function get($timestamp) {
        $timestamp = (int)$timestamp;
        $difference = $this->dateDifference($timestamp, time());
        if ($difference[self::YEARS] > 0 || $difference[self::MONTHS] > 2) {
            return date('d.m.Y', $timestamp);
        }
        $out_ago = '';
        if ($difference[self::DAYS] == 0) {
            $out_ago = 'сегодня';
        }
        else if ($difference[self::DAYS] == 1) {
            $out_ago = 'вчера';
        }
        else {
            if ($difference[self::DAYS] > 0) {
                $out_ago = Declension::number($difference[self::DAYS], 'день', 'дня', 'дней', true);
            }
            if ($difference[self::MONTHS] > 0) {
                $out_ago .= ' и '. Declension::number($difference[self::MONTHS], 'месяц', 'месяца', 'месяцев', true);
            }
            $out_ago .= ' назад';
        }
        return $out_ago;
    }

    /**
     * @param int $from
     * @param int $to
     * @return array
     */
    private function dateDifference($from, $to) {
        $diff = abs($to - $from);
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        return [
            self::YEARS => $years,
            self::MONTHS => $months,
            self::DAYS => $days,
        ];
    }
}