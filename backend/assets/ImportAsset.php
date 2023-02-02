<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class ImportAsset
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class ImportAsset extends AssetBundle {

    public $sourcePath = '@backend/web/js/import';

    public $js = [
        'dist/import.min.js',
    ];

    public $depends = [
        'common\assets\KnockoutAsset',
        'common\assets\UploaderAsset',
        'backend\assets\AppAsset',
        'common\libs\form\assets\ClientValidator',
    ];

}