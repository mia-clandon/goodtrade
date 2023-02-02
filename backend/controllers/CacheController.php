<?php

namespace backend\controllers;

/**
 * Class CacheController
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class CacheController extends BaseController {

    public function actionClear() {
        $this->layout = false;
        \Yii::$app->cache->flush();
        $this->successMessage('Кеш успешно очищен.');
        return $this->redirect(['site/index']);
    }
}