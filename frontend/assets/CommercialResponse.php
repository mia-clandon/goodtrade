<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\View;
use common\assets\KnockoutAsset;

/**
 * Class CommercialResponse
 * @package frontend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class CommercialResponse extends AssetBundle {

    public $jsOptions = ['position' => View::POS_HEAD];

    public $depends = [
        KnockoutAsset::class,
        PerfectScrollbar::class,
    ];
}