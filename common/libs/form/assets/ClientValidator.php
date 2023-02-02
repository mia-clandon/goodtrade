<?php

namespace common\libs\form\assets;

use yii\web\{AssetBundle, JqueryAsset};

/**
 * Class ClientValidator
 * @package common\libs\form\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class ClientValidator extends AssetBundle {

    public $sourcePath = '@bower/jquery-form-validator/form-validator';

    public $js = [
        'jquery.form-validator.min.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}