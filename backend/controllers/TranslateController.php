<?php

namespace backend\controllers;

use backend\assets\AppAsset;
use backend\forms\translate\HintUpdate;
use common\libs\i18n\models\Hint;
use yii\base\Exception;
use backend\forms\translate\Translate as TranslateForm;
use yii\helpers\Url;

/**
 * Class TranslateController
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class TranslateController extends BaseController {

    public function actionIndex(): string {
        \Yii::$app->getView()->registerJsFile('/js/translate/dist/index.js', [
            'depends' => AppAsset::class,
        ]);
        // ресурсы для формы добавления переводов.
        (new TranslateForm())
            ->setAjaxMode()
            ->loadResources();

        $model = new Hint();
        $data_provider = $model->search(\Yii::$app->request->get());

        return $this->render('index', [
            'model' => $model,
            'data_provider' => $data_provider,
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionUpdate(): string {
        $model = $this->getModel();
        $form = new HintUpdate();
        $form->setModel($model);

        if ($data = \Yii::$app->request->post()) {
            $form->setFormData($data);
            $form->validate();
            if ($form->isValid() && $form->save()) {
                $this->redirect(['translate/index', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'form' => $form->render(),
            'model' => $model,
        ]);
    }

    /**
     * Добавление перевода к хинту.
     * @return string|array
     * @throws
     */
    public function actionAddTranslate() {
        if (!\Yii::$app->request->isAjax) {
            throw new Exception(404, 'Page not found.');
        }
        $hint_id = (int)\Yii::$app->request->get('hint-id',
            \Yii::$app->request->post('hint-id')
        );
        $hint_text = Hint::getSourceText($hint_id);
        $this->layout = false;

        // форма обновления языковых версий хинта.
        $form = (new TranslateForm())
            ->setAction(Url::to(['/translate/add-translate']))
            ->setAjaxMode()
            ->setHintId($hint_id)
            ->setHintText($hint_text)
            ->setTemplateFileName('add-translate');

        if (\Yii::$app->request->isPost) {
            return $form->ajaxValidateAndSave();
        }

        return $this->render('add-translate', [
            'form' => $form->render(),
        ]);
    }

    //todo: delete.

    /**
     * @return Hint
     * @throws Exception
     */
    private function getModel(): Hint {
        $id = (int)\Yii::$app->request->get('id', 0);
        /** @var $model Hint */
        if ($id) {
            $model = Hint::findOne($id);
            if (!$model) {
                throw new Exception('Hint not found.');
            }
        }
        else {
            $model = new Hint();
        }
        return $model;
    }
}