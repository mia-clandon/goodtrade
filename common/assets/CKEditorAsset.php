<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class CKEditorAsset
 * @package common\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class CKEditorAsset extends AssetBundle {

    public $sourcePath = '@common/web/js/ckeditor';

    public $js = [
        'ckeditor.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}