<?php

namespace common\libs;

/**
 * Class VarDumper
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class VarDumper extends \yii\helpers\VarDumper {

   public static function dump($var, $depth = 10, $highlight = 1) {
       parent::dump($var, $depth, $highlight);
   }
}