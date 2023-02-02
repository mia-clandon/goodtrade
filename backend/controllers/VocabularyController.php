<?php

namespace backend\controllers;

use backend\assets\AppAsset;
use backend\forms\vocabulary\Update;
use common\assets\FormAsset;
use common\models\goods\Product;
use common\models\Vocabulary;
use yii\db\Exception;
use yii\helpers\Html;
use yii\web\Response;

/**
 * Class VocabularyController
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class VocabularyController extends BaseController
{

    /**
     * @return string
     */
    public function actionIndex()
    {
        FormAsset::register(\Yii::$app->getView());
        \Yii::$app->getView()->registerJsFile('/js/vocabulary/index.js', [
            'depends' => AppAsset::class,
        ]);

        $model = new Vocabulary();
        $data_provider = $model->search(\Yii::$app->request->get());

        return $this->render('index', [
            'model' => $model,
            'data_provider' => $data_provider,
        ]);
    }

    /**
     * @return string
     */
    public function actionUpdate()
    {
        $model = $this->getModel();

        $form = (new Update())
            ->setModel($model)
            ->setPostMethod()
            ->setTemplateFileName('update');

        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $form->setFormData(\Yii::$app->request->post());
            return $form->ajaxValidateAndSave();
        }

        if ($data = \Yii::$app->request->post()) {

            $form->setFormData($data);
            $form->validate();

            if ($form->isValid() && $form->save()) {
                $this->redirect(['vocabulary/update', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'form' => $form->render(),
            'model' => $model,
        ]);
    }

    /**
     * @throws Exception
     */
    public function actionDelete()
    {
        $id = (int)\Yii::$app->request->get('id', 0);
        /**
         * @var $model Vocabulary
         */
        $model = Vocabulary::findOne($id);
        if (!$model) {
            throw new Exception('Vocabulary not found.');
        }
        if ($model->useInProduct()) {
            /** @var Product[] $products */
            $products = $model->getProducts()
                ->limit(50)
                ->all();
            $result = [
                Html::tag('strong', 'Ошибка удаления !'),
                'Характеристика используется в товарах:'
            ];
            foreach ($products as $product) {
                $result[] = Html::a($product->getTitle(), ['/product/update', 'id' => $product->id]);
            }
            $this->errorMessage(implode('<br />', $result));
            return $this->goBack();
        }
        $model->delete();
        return $this->redirect(['vocabulary/index']);
    }

    public function goBack($defaultUrl = null)
    {
        $referrer = \Yii::$app->request->referrer;
        return $this->redirect($referrer ? $referrer : ['vocabulary/index']);
    }

    /**
     * Список характеристик.
     * @return array|\yii\db\ActiveRecord[]
     * @throws \yii\base\Exception
     */
    public function actionGet()
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $vocabulary_list = Vocabulary::find()
                ->select(['id', 'title'])
                ->asArray()
                ->all();
            return $vocabulary_list;
        }
        throw new \yii\base\Exception(404, 'page not found');
    }

    /**
     * Индексирование характеристик.
     * @return Response
     */
    public function actionIndexAll()
    {
        $this->layout = false;
        /** @var Vocabulary[] $vocabularies */
        $vocabularies = Vocabulary::find()->all();
        foreach ($vocabularies as $vocabulary) {
            $vocabulary->updateSphinxIndex();
        }
        \Yii::$app->getSession()->setFlash(BaseController::FLASH_MESSAGE_SUCCESS, 'Индекс обновился успешно.');
        return $this->redirect(['vocabulary/index']);
    }

    /**
     * @return Vocabulary
     * @throws Exception
     */
    public function getModel()
    {
        $id = (int)\Yii::$app->request->get('id', 0);
        /**
         * @var $model Vocabulary
         */
        if ($id) {
            $model = Vocabulary::findOne($id);
            if (!$model) {
                throw new Exception('Характеристика отсутствует');
            }
        } else {
            $model = new Vocabulary();
        }
        return $model;
    }
}