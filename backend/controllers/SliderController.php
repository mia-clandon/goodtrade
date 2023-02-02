<?php

namespace backend\controllers;

use common\models\firms\Firm;
use common\models\MainSlider;

use backend\assets\AppAsset;
use backend\forms\slider\Update;

use yii\base\Exception;
use yii\web\Response;

/**
 * Class SliderController
 * @package backend\controllers
 * @author yerganat
 */
class SliderController extends BaseController {

    /**
     * @return string
     */
    public function actionIndex() {

        \Yii::$app->getView()->registerJsFile('/js/slider/index.js', [
            'depends' => AppAsset::class,
        ]);


        $slides = MainSlider::find()
            ->where(['slide_id' => 0])
            ->orderBy(['id' => 'asc'])
            ->all();

        $array_by_slide = [];

        foreach ($slides as $slide) {
            $parts = MainSlider::find()
                ->where(['slide_id' => $slide->id])
                ->orderBy('id')
                ->all();

            $array_by_slide[$slide->id] = $parts;

        }

        return $this->render('index', [
            'slides' => $slides,
            'array_by_slide' => $array_by_slide,
        ]);
    }

    /**
     * Обновление данных для слайдера.
     * @return string
     * @throws Exception
     */
    public function actionUpdate() {
        \Yii::$app->getView()->registerJsFile('/js/slider/index.js', [
            'depends' => AppAsset::class,
        ]);

        $model = $this->getModel();
        $slide_id = (int)\Yii::$app->request->get('slide_id', 0);
        $model->slide_id = $slide_id;

        if($slide_id == 0) {
            $form = (new Update())
                ->setModel($model)
                ->setPostMethod()
                ->setIsSlide();
        }
        else {
            $form = (new Update())
                ->setModel($model)
                ->setPostMethod()
                ->setTemplateFileName('update')
                ->addTemplateVars([
                    'tags' => $model->getTags(),
                    'tag' => $model->isNewRecord? 'banner' : $model->tag,
                    'types' => MainSlider::getTypes(),
                    'type' => $model->type,
                    'not_allowed_types' => $model->getNotAllowedType(),
                    'weight' => $model::SLIDER_WEIGHT,
                    'type_error_string' => '',
                ]);
        }

        // сохранение записи.
        if ($data = \Yii::$app->request->post()) {
            $form->setFormData($data);
            $form->validate();
            if ($form->isValid() && $form->save()) {
                if ($model->isNewRecord) {
                    $this->refresh();
                }
                else {
                    $this->redirect(['slider/update', 'id' => $model->id, 'slide_id' => $slide_id]);
                }
            }
            else {
                $form->addTemplateVars([
                    'tag' =>  $data['tag'],
                    'type' =>  $data['type'],
                    'type_error_string' =>  implode(',', $form->getErrors('type')),
                    'not_allowed_types' => $model->getNotAllowedType(),
                    'weight' => $model::SLIDER_WEIGHT,
                ]);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'form' => $form->render(),
        ]);
    }


    /**
     * Action Получает описание компании по id
     * @return array
     * @throws Exception
     */
    public function actionGetAjaxFirmDesc() {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception(404, 'Страница не найдена.');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $firm_id = (int)\Yii::$app->request->post('firm_id');
        $firm = Firm::findOne(['id' => $firm_id]);

        if (null !== $firm) {
            return [
                'message' => 'success',
                'description' => $firm->text,
            ];
        }
        return ['message' => 'error'];
    }

    /**
     * @return Response
     * @throws Exception
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete() {
        $model = $this->getModel();
        if ($model->isNewRecord) {
            throw new Exception(404, 'Страница не найдена.');
        }
        $model->delete();
        return $this->redirect(['slider/index']);
    }

    /**
     * @return MainSlider
     * @throws Exception
     */
    private function getModel() {
        $id = (int)\Yii::$app->request->get('id', 0);
        /**
         * @var $model MainSlider
         */
        if ($id) {
            $model = MainSlider::findOne($id);
            if (!$model) {
                throw new Exception('Записи не существует.');
            }
        }
        else {
            $model = new MainSlider();
        }
        return $model;
    }
}