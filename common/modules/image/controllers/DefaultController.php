<?php

namespace common\modules\image\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use common\modules\image\helpers\Image;

/**
 * Class DefaultController
 * @package common\modules\image\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class DefaultController extends Controller {

    const STORAGE_DIRECTORY = 'storage';

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action) {
        $this->layout = false;
        return parent::beforeAction($action);
    }

    /**
     * @throws \Exception
     */
    public function actionIndex() {
        $params = Yii::$app->request->get();
        if (empty($params)) {
            throw new \Exception('Page not found');
        }
        try {
            $params = $this->prepareParams($params);
            $absolute_thumbnail_path = Image::getInstance()->setParams($params)->get();
            return $this->responseImage($absolute_thumbnail_path);
        }
        catch (\Exception $exception) {
            Yii::error($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Подготавливает параметры в понятный вид.
     * @param array $params
     * @return array
     */
    private function prepareParams(array $params) {

        $sizes = explode('_', ArrayHelper::getValue($params, 'size', ''));
        // данные для ресайза.
        $width = ArrayHelper::getValue($sizes, 1, null);
        $height = ArrayHelper::getValue($sizes, 2, null);

        $params = [
            Image::FILE_NAME        => ArrayHelper::getValue($params, 'file'),
            Image::FILE_EXTENSION   => strtolower(ArrayHelper::getValue($params, 'ext')),
            Image::REQUEST_LINK     => Url::current([], false),
            Image::RESIZE_KEY => [
                Image::RESIZE_MODE      => $params[Image::RESIZE_MODE] ?? null,
                Image::RESIZE_WIDTH     => $width ? (int)$width : null,
                Image::RESIZE_HEIGHT    => $height ? (int)$height : null,
            ],
        ];
        return $params;
    }

    /**
     * @param string $image
     * @return \yii\console\Response|Response
     */
    public function responseImage(string $image): Response {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $extension = pathinfo($image, PATHINFO_EXTENSION);
        Yii::$app->response->headers->add('content-type', 'image/'. $extension);
        Yii::$app->response->data = file_get_contents($image);
        return Yii::$app->response;
    }
}