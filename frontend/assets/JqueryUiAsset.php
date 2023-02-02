<?php

namespace frontend\assets;

use yii\web\View;

/**
 * Class JqueryUiAsset
 * @package frontend\assets
 */
class JqueryUiAsset extends \common\assets\JqueryUiAsset {

    public $jsOptions = ['position' => View::POS_HEAD];

    protected function getDependsOn(): array {
        return array_merge(parent::getDependsOn(), [
            AppAsset::class,
        ]);
    }
}