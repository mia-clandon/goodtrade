<?php

namespace backend\controllers;

use yii\web\Response;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use common\models\OkedRelation;
use common\models\search\Oked as OkedSearch;
use common\models\Oked;

use backend\forms\oked\Search;

/**
 * Class OkedController
 * Контроллер для работы с ОКЭД.
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class OkedController extends BaseController {

    /**
     * Каталог ОКЭД
     * @return string
     */
    public function actionIndex() {
        $model = new Oked();
        $request_data = \Yii::$app->request->get();
        $data_provider = $model->search($request_data);
        $search_form = (new Search())
            ->addDataAttribute('url', Url::to(['oked/index']))
            ->setTitle('Поиск по ОКЭД')
            ->setGetMethod()
            ->setFormMode(Search::FORM_HORIZONTAL_MODE)
            ->setTemplateFileName('index')
        ;
        if ($request_data) {
            $search_form->setFormData($request_data);
        }
        return $this->render('index', [
            'data_provider' => $data_provider,
            'search_form' => $search_form->render(),
        ]);
    }

    /**
     * Связывание ОКЭД'ов.
     * @return string
     */
    public function actionRelation() {
        \Yii::$app->getView()->registerJsFile('/js/oked/relation.js', ['depends' => ['backend\assets\AppAsset']]);
        $oked_list = Oked::find()
            ->orderBy('key ASC')
            ->all();
        /** @var OkedRelation[] $related_oked_list */
        $related_oked_list = OkedRelation::find()
            ->all();
        $related_oked_list_filtered = [];
        foreach ($related_oked_list as $oked) {
            $related_oked_list_filtered[$oked->from_key][] = $oked->to_key;
        }
        unset($related_oked_list);
        return $this->render('relation', [
            'oked_list' => $oked_list,
            'related_oked_list_filtered' => $related_oked_list_filtered,
        ]);
    }

    /**
     * Поиск ОКЭД по номеру/названию.
     * @return string
     * @throws Exception
     */
    public function actionFindOked() {
        $this->layout = false;

        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        $key = intval(\Yii::$app->request->post('key'));
        $name = \Yii::$app->request->post('name');

        $search = (new OkedSearch())
            ->setFilterKey($key)
            ->setFilterTitle($name);

        $oked_list = $search->get()->all();

        return $this->render('parts/oked_result', [
            'oked_list' => $oked_list,
        ]);
    }

    /**
     * Поиск ОКЭД по номеру/названию.
     * @return string
     * @throws Exception
     */
    public function actionFindMain() {
        $this->layout = false;

        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }
        $text = (string)\Yii::$app->request->post('text');

        $search = new OkedSearch();

        if(is_numeric($text)) {
            $search->setFilterKey($text);
        }
        else {
            $search->setFilterTitle($text);
        }
        $oked_list = $search->get()->all();

        return $this->render('parts/oked_list', [
            'oked_list' => $oked_list,
            'with_checkbox' => false,
            'with_delete' => false,
            'with_reveal' => false,
        ]);
    }

    /**
     * Поиск связанных ОКЭД по номеру.
     * @return string
     * @throws Exception
     */
    public function actionFindLinks() {
        $this->layout = false;

        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        $code = (int)\Yii::$app->request->post('code');
        $is_customer = (int)\Yii::$app->request->post('is_customer');

        if ($is_customer) {
            $relation = OkedRelation::find()->where(['=', 'to_key', $code])->select('from_key');
        }
        else {
            $relation = OkedRelation::find()->where(['=', 'from_key', $code])->select('to_key');
        }

        $oked_list = Oked::find()
            ->orderBy('key ASC')
            ->where(['in', 'key', $relation])
            ->all();

        return $this->render('parts/oked_list', [
            'oked_list' => $oked_list,
            'with_checkbox' => false,
            'with_delete' => true,
            'with_reveal' => true,
        ]);
    }

    /**
     * Поиск ОКЭД по для связи.
     * @return string
     * @throws Exception
     */
    public function actionFindAdd() {
        $this->layout = false;

        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        $code = (int)\Yii::$app->request->post('code');
        $text = (string)\Yii::$app->request->post('text');
        $is_customer= (int)\Yii::$app->request->post('is_customer');

        $search = new OkedSearch();

        if ($is_customer) {
            $exist_ids = OkedRelation::find()->where(['=', 'to_key', $code])->asArray()->select('from_key')->all();
            $exist_ids = array_map('intval', ArrayHelper::getColumn($exist_ids, 'from_key'));
        }
        else {
            $exist_ids = OkedRelation::find()->where(['=', 'from_key', $code])->asArray()->select('to_key')->all();
            $exist_ids = array_map('intval', ArrayHelper::getColumn($exist_ids, 'to_key'));
        }

        $exist_ids[] = $code;
        $search->setFilterNotExistCodes($exist_ids);

        if(is_numeric($text)) {
            $search->setFilterKey($text);
        }
        else {
            $search->setFilterTitle($text);
        }
        $oked_list = $search->get()->all();

        return $this->render('parts/oked_list', [
            'oked_list' => $oked_list,
            'with_checkbox' => true,
            'with_delete' => false,
            'with_reveal' => true,
        ]);
    }

    /**
     * Создание связей ОКЕД.
     * @return array
     * @throws Exception
     */
    public function actionCreateRelations() {
        $this->layout = false;

        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $from = (array)\Yii::$app->request->post('from');
        $from = array_map('intval', $from);
        $to = (array)\Yii::$app->request->post('to');
        $to = array_map('intval', $to);

        foreach ($to as $to_oked) {
            foreach ($from as $from_oked) {
                $relation = new OkedRelation();
                $relation->from_key = $from_oked;
                $relation->to_key = $to_oked;
                $relation->save();
            }
        }
        return ['success' => true];
    }

    /**
     * Удаление связи.
     * @throws Exception
     */
    public function actionRemoveRelation() {
        $this->layout = false;

        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $from = (int)\Yii::$app->request->post('from');
        $to = (int)\Yii::$app->request->post('to');

        $result = OkedRelation::deleteAll(['from_key' => $from, 'to_key' => $to]);
        return [
            'success' => $result,
        ];
    }

    /**
     * Переиндексация.
     */
    public function actionUpdateIndex() {
        Oked::indexAll();
        \Yii::$app->getSession()->setFlash(BaseController::FLASH_MESSAGE_SUCCESS, 'Индекс обновился успешно.');
        return $this->redirect(['oked/index']);
    }
}