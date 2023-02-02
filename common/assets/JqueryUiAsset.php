<?php

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class JqueryUiAsset
 * @package frontend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class JqueryUiAsset extends AssetBundle {

    public $sourcePath = '@bower/jquery-ui';

    public $js = [
        'jquery-ui.min.js',
    ];

    public $css = [
        //TODO: по мере необходимости сюда добавлять нужные стили для JQueryUI.
        //'themes/base/base.css',
        'themes/base/jquery-ui.min.css',
    ];

    public $depends = [];

    public function init() {
        parent::init();
        $this->depends = $this->getDependsOn();
    }

    protected function getDependsOn(): array {
        return [
            JqueryAsset::class,
        ];
    }
}