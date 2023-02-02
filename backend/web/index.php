<?php /** @noinspection DuplicatedCode */

mb_internal_encoding("UTF-8");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../functions.php');

defined('YII_DEBUG') or define('YII_DEBUG', (bool)env('YII_DEBUG'));
defined('YII_ENV') or define('YII_ENV', env('YII_ENV'));
defined('BASE_DOMAIN') or define('BASE_DOMAIN', env('BASE_DOMAIN'));

require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

if (!(bool)env('IS_ENABLED_SITE')) {
    echo '<p>In development.</p>';
    exit;
}

try {
    $application = new yii\web\Application(require(__DIR__ . '/../../config/backend/config.php'));
    $application->run();
} catch (\yii\base\InvalidConfigException $e) {
}