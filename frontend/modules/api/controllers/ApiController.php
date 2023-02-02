<?php /** @noinspection PhpMissingReturnTypeInspection */

/** @noinspection PhpMissingParamTypeInspection */

namespace frontend\modules\api\controllers;

use Yii;
use yii\base\Exception;
use yii\web\{Controller, Response};

/**
 * Class ApiController
 * Базовый Api Controller.
 * @package frontend\modules\api\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class ApiController extends Controller {

    //TODO: константы для кодов ошибок.

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action) {
        // Без Layout
        $this->layout = false;

        if (!Yii::$app->request->isAjax) {
            throw new Exception('Not found.');
        }

        // Без CSRF валидации.
        $this->enableCsrfValidation = false;
        // JSON ответы.
        Yii::$app->response->format = Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }
}