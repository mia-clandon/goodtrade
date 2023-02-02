<?php

namespace frontend\controllers;

use common\models\Chrono;
use yii\web\Response;
use common\models\firms\Firm;
use common\models\goods\Product;
use frontend\assets\PerfectScrollbar;
use frontend\components\lib\FavoriteProcessor;
use yii\base\Exception;

/**
 * Class FavoriteController
 * @package frontend\controllers
 * @author yerganat
 */
class FavoriteController extends BaseController {

    private function registerPageScripts() {
        $this->registerScriptBundle();
        PerfectScrollbar::register($this->getView());
    }

    /**
     * Страница сравнения.
     * @return string
     * @throws Exception
     */
    public function actionIndex() {

        $this->registerPageScripts();

        $firm_id = \Yii::$app->request->get('firm_id');
        $firm = Firm::findOne((int)$firm_id);

        $firm_product_array = FavoriteProcessor::i()
            ->getFavoriteIds();
        $firms = Firm::find()->where(['id' => array_keys($firm_product_array)])->all();

        if (empty($firm_product_array)) {
            return $this->render('empty', []);
        }

        if(!is_null($firm_id) && !array_key_exists((int)$firm_id, $firm_product_array)) {
            throw new Exception(404);
        }

        $products = [];
        if(!empty($firm_product_array[(int)$firm_id])) {
            $products = Product::find()->where(['id' => array_values($firm_product_array[(int)$firm_id])])->all();
        }

        $chronos = Chrono::find()->where(['firm_id' =>$firm_id])->andWhere(['>', 'created_at', time()])->orderBy('created_at desc')->all();

        return $this->render('index', [
            'firms' => $firms,
            'firm' => $firm,
            'firm_id' => $firm_id,
            'firm_product_array' => $firm_product_array,
            'products' => $products,
            'chronos' => $chronos,
        ]);
    }

    /**
     * Подгрузка товара по ajax.
     * @throws Exception
     * @return string
     */
    public function actionGetProductList() {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        $firm_id = (int)\Yii::$app->request->post('firm_id', 0);

        $firm_product_array = FavoriteProcessor::i()
            ->getFavoriteIds();

        $products = [];
        if(!empty($firm_product_array[(int)$firm_id])) {
            $products = Product::find()->where(['id' => array_values($firm_product_array[(int)$firm_id])])->all();
        }

        return $this->render('parts/products', [
            'products'  => $products,
            'firm_id' => null
        ]);
    }

    /**
     * Подгрузка хронологии по ajax.
     * @throws Exception
     * @return string
     */
    public function actionGetChronosList() {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        $firm_id = (int)\Yii::$app->request->post('firm_id', 0);

        $chronos = Chrono::find()->where(['firm_id' =>$firm_id])->orderBy('created_at desc')->all();

        return $this->render('parts/chronos', [
            'chronos' => $chronos
        ]);
    }

    /**
     * Подгрузка хронологии по ajax.
     * @throws Exception
     * @return array|Response
     */
    public function actionSetUserNote() {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        $note = \Yii::$app->request->post('note');
        $firm_id = (int)\Yii::$app->request->post('firm_id', 0);

        $session = \Yii::$app->session;
        $session->set('note'.$firm_id, $note);

        \Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'result' => true
        ];
    }
}