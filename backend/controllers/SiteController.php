<?php

namespace backend\controllers;

use yii;
use yii\web\Response;
use backend\forms\Sign;

/**
 * Site controller
 */
class SiteController extends BaseController {
    
    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * Страничка входа в панель управления.
     * @return string
     */
    public function actionSignIn() {

        if (Yii::$app->getUser()->id) {
            return $this->redirect(['site/index']);
        }

        $this->layout = 'sign';

        $form = (new Sign())
            ->setTemplateFileName('sign')
        ;

        if (Yii::$app->request->isPost) {
            $form_data = Yii::$app->request->post();
            $form->setFormData([
                'username' => yii\helpers\ArrayHelper::getValue($form_data, 'username'),
                'password' => yii\helpers\ArrayHelper::getValue($form_data, 'password'),
            ]);
            $form->validate();
            if ($form->isValid() && $form->sign()) {
                return $this->redirect(['site/index']);
            }
        }

        return $this->render('sign', [
            'sign_form' => $form->render(),
        ]);
    }

    /**
     * Выход.
     * @return Response
     */
    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->redirect(['/sign-in']);
    }
}