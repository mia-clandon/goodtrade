<?php

namespace frontend\modules\api\controllers;

use yii\web\UploadedFile;
use yii\web\Response;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use common\libs\uploader\Handler as UploadHandler;
use frontend\models\Uploader;
use backend\components\qq\FileUploader;

/**
 * Class UploaderController
 * @package frontend\modules\api\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class UploaderController extends ApiController {

    const SUCCESS_KEY = 'success';
    const ERRORS_KEY = 'errors';
    const FILE_NAME_KEY = 'file_name';
    const FILE_PATH_KEY = 'file_path';

    /**
     * Загрузчик QQ.
     * @return array
     */
    public function actionQqUploader() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $uploader = new FileUploader([
            'jpeg', 'jpg', 'png',
        ]);
        return $uploader->handleUpload(\Yii::getAlias('@app/web/files/'), true);
    }

    /**
     * Загрузчик файлов.
     * todo
     * @return array
     */
    public function actionUploadFile() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        //todo: загрузка > 1 файлов.
        //todo: получение данных с контрола (форматы/размер файла.)
        $model = new Uploader();
        if (\Yii::$app->request->isPost) {
            $file_name_attribute = \Yii::$app->request->post('file_name');
            $model->file = arr_get_val(UploadedFile::getInstancesByName($file_name_attribute), 0, null);
            if ($model->validate()) {
                $model->upload();
                return [
                    self::SUCCESS_KEY => true,
                    self::FILE_NAME_KEY => $model->file_name,
                    self::FILE_PATH_KEY => $model->file_path,
                ];
            }
            else {
                return [
                    self::SUCCESS_KEY => false,
                    self::ERRORS_KEY => arr_get_val($model->getErrors(), 'file', []),
                ];
            }
        }
        return [self::SUCCESS_KEY => false];
    }

    /**
     * Загрузчик. (JQuery File Uploader) по умолчанию.
     */
    public function actionUpload() {

        $options = [
            'accept_file_types' => '/\.(gif|jpe?g|png)$/i',
        ];

        try {

            $thumbnails = \Yii::$app->request->post('thumbnails');
            $options['param_name'] = \Yii::$app->request->post('param_name', 'files');

            if (null !== $thumbnails) {

                // размеры миниатюр.
                $thumbnails = Json::decode($thumbnails);

                $width = arr_get_val($thumbnails, 'width', false);
                $height = arr_get_val($thumbnails, 'height', false);

                // тип ресайза
                $resize_type = arr_get_val($thumbnails, 'type', 'NONE');

                // установка опций.
                $options['image_versions'] = [
                    'thumbnail' => [
                        'width'     => $width,
                        'height'    => $height,
                        'type'      => $resize_type,
                    ]
                ];
            }
            new UploadHandler($options);
        }
        catch (Exception $e) {
            \Yii::error($e->getMessage(), 'apiRequest');
        }
    }
}