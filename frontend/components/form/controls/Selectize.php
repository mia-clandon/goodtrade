<?php

namespace frontend\components\form\controls;

use backend\components\form\controls\Selectize as BackendSelectize;
use frontend\assets\SelectizeAsset;
use frontend\assets\SelectizeDisableAsset;

/**
 * Class Selectize
 * @package frontend\components\form\controls
 */
class Selectize extends BackendSelectize {

    protected $template_name = 'selectize';

    protected function registerFormAssets() {
        SelectizeAsset::register(\Yii::$app->getView());
        SelectizeDisableAsset::register(\Yii::$app->getView());
    }
}