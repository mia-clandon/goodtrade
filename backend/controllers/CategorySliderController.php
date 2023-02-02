<?php

namespace backend\controllers;

use common\models\CategorySlider;
use yii\base\Exception;


use backend\forms\category\Slider;
use yii\web\Response;

/**
 * Class CategorySliderController
 * @package backend\controllers
 * @author yerganat
 */
class CategorySliderController extends BaseController {

    /**
     * @return string
     */
    public function actionIndex() {

        $model = new CategorySlider();
        $data_provider = $model->search(\Yii::$app->request->get());

        return $this->render('index', [
            'model' => $model,
            'data_provider' => $data_provider,
        ]);
    }

    /**
     * Обновление данных для слайдера.
     * @return string
     * @throws Exception
     */
    public function actionUpdate() {
        $model = $this->getModel();

        $form = (new Slider())
            ->setModel($model)
            ->setPostMethod();

        // сохранение записи.
        if ($data = \Yii::$app->request->post()) {
            $form->setFormData($data);
            $form->validate();
            if ($form->isValid() && $form->save()) {
                if ($model->isNewRecord) {
                    $this->refresh();
                }
                else {
                    $this->redirect(['category-slider/update', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'form' => $form->render(),
        ]);
    }

    /**
     * Удаление данных для слайдера.
     * @return \yii\web\Response
     * @throws Exception
     */
    public function actionDelete() {
        $model = $this->getModel();
        if ($model->isNewRecord) {
            throw new Exception(404, 'Страница не найдена.');
        }
        $model->delete();
        return $this->redirect(['category-slider/index']);
    }

    /**
     * Удаление фотографии слайда.
     * @throws \yii\base\Exception
     */
    public function actionRemoveImage() {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new \yii\base\Exception('Страница не найдена.');
        }
        $id = \Yii::$app->request->post('entity_id');
        if (!$id) {
            throw new \yii\base\Exception('Страница не найдена.');
        }
        /** @var CategorySlider $model */
        $model = CategorySlider::findOne($id);
        $model->clearImage();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => $model->save()];
    }

    /**
     * @return CategorySlider
     * @throws Exception
     */
    private function getModel() {
        $id = (int)\Yii::$app->request->get('id', 0);
        /**
         * @var $model CategorySlider
         */
        if ($id) {
            $model = CategorySlider::findOne($id);
            if (!$model) {
                throw new Exception('Записи не существует.');
            }
        }
        else {
            $model = new CategorySlider();
        }
        return $model;
    }
}