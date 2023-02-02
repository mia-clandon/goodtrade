<?php

namespace backend\controllers;

use yii\db\Exception;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

use common\models\goods\Product;
use common\models\Vocabulary;
use common\models\VocabularyOption;

use backend\forms\term\Update;

/**
 * Class TermController
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class TermController extends BaseController {

    /** @var null|Vocabulary */
    private $vocabulary;

    /**
     * @return string
     * @throws Exception
     */
    public function actionUpdate() {

        $option_id = \Yii::$app->request->get('id', \Yii::$app->request->post('id', 0));

        $vocabulary_id = \Yii::$app->request->get('vocabulary_id', \Yii::$app->request->post('vocabulary_id', 0));
        if (!$vocabulary_id) {
            throw new Exception('Характеристика не известна.');
        }

        $vocabulary = Vocabulary::findOne($vocabulary_id);
        if (!$vocabulary) {
            throw new Exception('Характеристика не известна.');
        }

        if (!$vocabulary->isSelectType()) {
            throw new Exception('К данному типу характеристики нельзя добавлять возможные значения.');
        }

        $form = (new Update())
            ->setModel($this->getModel())
            ->setPostMethod()
            ->setVocabulary($vocabulary)
        ;

        if (\Yii::$app->request->isAjax && \Yii::$app->request->isGet) {
            $this->layout = false;
            $form->setAjaxMode()
                ->setAction(Url::to(['/term/update', 'id' => $option_id]));
            return $form->render();
        }

        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $form->setFormData(\Yii::$app->request->post());
            return $form->ajaxValidateAndSave();
        }

        if ($data = \Yii::$app->request->post()) {

            $form->setFormData($data);
            $form->validate();

            if ($form->isValid() && $form->save()) {
                $this->redirect(['vocabulary/update', 'id' => $vocabulary->id]);
            }
        }

        return $this->render('update', [
            'form' => $form->render(),
            'model' => $this->getModel(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function actionDelete() {
        $id = (int)\Yii::$app->request->get('id', 0);

        /**
         * @var VocabularyOption $model
         */
        $model = VocabularyOption::findOne($id);
        if (!$model) {
            throw new Exception('Vocabulary option not found.');
        }

        if ($model->useInProduct()) {
            /** @var Product[] $products */
            $products = $model->getProductListQuery()
                ->select(['id', 'title'])
                ->limit(50)
                ->all();
            $result = [
                Html::tag('strong', 'Ошибка удаления !'),
                'Значение характеристики используется в товарах:'
            ];
            foreach ($products as $product) {
                $result[] = Html::a($product->getTitle(), ['/product/update', 'id' => $product->id]);
            }
            $this->errorMessage(implode('<br />', $result));
            return $this->goBack();
        }

        $model->delete();
        return $this->redirect(['vocabulary/update', 'id' => $model->vocabulary_id]);
    }

    public function goBack($defaultUrl = null) {
        $referrer = \Yii::$app->request->referrer;
        return $this->redirect($referrer ? $referrer : ['vocabulary/update', 'id' => (int)\Yii::$app->request->get('id', 0)]);
    }

    /**
     * Возвращает VocabularyOption модель.
     * @return VocabularyOption
     * @throws Exception
     */
    public function getModel() {

        $id = (int)\Yii::$app->request->get('id', 0);
        /** @var VocabularyOption $vocabulary_option_model */
        $vocabulary_option_model = new VocabularyOption();
        if ($id) {
            $vocabulary_option_model = VocabularyOption::findOne($id);
            if (!$vocabulary_option_model) {
                throw new Exception('Значение характеристики отсутствует');
            }
        }
        return $vocabulary_option_model;
    }

    /**
     * @return Vocabulary|null
     * @throws Exception
     */
    public function getVocabularyModel() {
        if (null !== $this->vocabulary) {
            return $this->vocabulary;
        }
        $vocabulary_id = \Yii::$app->request->get('vocabulary_id', \Yii::$app->request->post('vocabulary_id', 0));
        if (!$vocabulary_id) {
            throw new Exception('Характеристика не известна.');
        }
        return $this->vocabulary = Vocabulary::findOne($vocabulary_id);
    }
}