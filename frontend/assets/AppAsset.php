<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace frontend\assets;

use yii\web\{AssetBundle, View};

use common\assets\LazyLoadAsset;

/**
 * Файл зависимостей приложения от скриптов common.js
 * @package frontend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class AppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    // основные скрипты в начале страницы.
    public $jsOptions = ['position' => View::POS_HEAD];

    public $css = [
        'css_new/main.min.css',
    ];

    public $js = [
        //'js/libs/modernizr-custom.js',

        // todo: перенести скрипты в bower, сделать их минификацию на prod. + перетащить в Asset's.
        'js/libs/jquery.form-validator.min.js',
        'js/libs/jquery.mask.js',
        'js/libs/jquery.cookie.js',
        'js/libs/notify.min.js',
        'js/libs/jquery.nanoscroller.min.js',
        'js/libs/slick.min.js',
        'js/libs/modernizr-custom.js',
        'js/libs/google.tags.js',
        'js/libs/perfect-scrollbar.jquery.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'common\assets\JqueryUiAsset',
        LazyLoadAsset::class,
    ];
}