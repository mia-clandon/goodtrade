<?php

namespace console\controllers;

use common\models\User;
use Yii;
use yii\console\Controller;

/**
 * Class RbacController
 * @package console\controllers
 */
class RbacController extends Controller {

    const ADMIN_ACCOUNT = 'trade@goodtrade.kz';

    public function actionInit() {

        $auth = Yii::$app->authManager;

        $admin = $auth->createRole('admin');
        $auth->add($admin);

        $user = User::findByUsername(self::ADMIN_ACCOUNT);
        if ($user) {
            $auth->assign($admin, $user->id);
        }
    }
}