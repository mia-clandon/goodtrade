<?php

namespace backend\controllers;

use backend\forms\test\Test;

/**
 * Class TestController
 * Контроллер для тестирования ф-ла.
 * @package backend\controllers
 */
class TestController extends BaseController {

    public function actionIndex() {
        $form = new Test();
        if ($data = \Yii::$app->request->post()) {
            $form->setFormData($data);
            $form->validate();
        }
        return $this->render('index', [
            'form' => $form->render(),
        ]);
    }
}