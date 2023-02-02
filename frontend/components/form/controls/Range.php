<?php

namespace frontend\components\form\controls;

use frontend\assets\JqueryUiAsset;

/**
 * Class Range
 * @package frontend\components\form\controls
 */
class Range extends \backend\components\form\controls\Range {

    protected $template_name = 'range';

    protected function registerFormAssets() {
        JqueryUiAsset::register(\Yii::$app->getView());
    }
}