<?php

namespace backend\components\form\controls;

/**
 * Class Bootstrap
 * @package backend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Bootstrap {

    #region - размеры контролов.
    const COL_SM_2  = 'col-sm-2';
    const COL_SM_4  = 'col-sm-4';
    const COL_SM_6  = 'col-sm-6';
    const COL_SM_8  = 'col-sm-8';
    const COL_SM_10 = 'col-sm-10';
    const COL_SM_12 = 'col-sm-12';
    #endregion.

    /**
     * @return array
     */
    public static function getSizes() {
        return [
            self::COL_SM_2,
            self::COL_SM_4,
            self::COL_SM_6,
            self::COL_SM_8,
            self::COL_SM_10,
            self::COL_SM_12,
        ];
    }
}