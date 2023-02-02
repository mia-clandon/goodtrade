<?php

namespace backend\assets;

use yii\web\{AssetBundle, JqueryAsset};

/**
 * Class JQueryCookie
 * @package backend\assets
 */
class JQueryCookie extends AssetBundle
{
    public $sourcePath = '@bower/jquery-cookie';

    public $js = [
        'jquery.cookie.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}