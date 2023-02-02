<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class BootstrapTreeViewAsset
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class BootstrapTreeViewAsset extends AssetBundle {

    public $sourcePath = '@bower/bootstrap-treeview/dist';

    public $css = [
        'bootstrap-treeview.min.css',
    ];

    public $js = [
        'bootstrap-treeview.min.js',
    ];

    public $depends = [
        'backend\assets\BootstrapAsset',
    ];
}