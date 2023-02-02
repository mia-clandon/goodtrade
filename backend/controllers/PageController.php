<?php

namespace backend\controllers;

use backend\forms\page\Update;
use common\libs\StringHelper;
use common\models\Page;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\UploadedFile;


/**
 * Class PageController
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class PageController extends BaseController
{

    const JSON_EXTENSION = 'json';
    const JSON_APPLICATION_TYPE = 'application/json';

    public function actionIndex()
    {
        $model = new Page();
        $data_provider = $model->search(\Yii::$app->request->get());
        return $this->render('index', [
            'data_provider' => $data_provider,
        ]);
    }

    //TODO: импорт / экспорт.

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

        if ($data = \Yii::$app->request->post()) {

            $form->setFormData($data);
            $form->validate();

            if ($form->isValid() && $form->save()) {

                if ($model->isNewRecord) {
                    $this->refresh();
                } else {
                    $this->redirect(['page/update', 'id' => $model->id]);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
            'form' => $form->render(),
        ]);
    }

    /**
     * Экспорт страниц сайта.
     */
    public function actionExport()
    {
        $this->layout = false;
        $data = (new Page())->export();
        \Yii::$app->response->sendContentAsFile($data, 'pages.json');
    }

    /**
     * Импортирование страниц сайта.
     * @return \yii\web\Response
     * @throws Exception
     */
    public function actionImport()
    {
        if (!\Yii::$app->request->post()) {
            throw new Exception('Page not found.');
        }
        $file = UploadedFile::getInstanceByName('json_file');
        if ($file->extension != self::JSON_EXTENSION ||
            $file->type != self::JSON_APPLICATION_TYPE
        ) {
            $this->errorMessage('Тип файла не корректный.');
            return $this->redirect(['/page/index']);
        }
        if ($file->error > 0) {
            $this->errorMessage('Произошла ошибка при загрузке файла.');
            return $this->redirect(['/page/index']);
        }
        $content = file_get_contents($file->tempName);
        if (!StringHelper::isJson($content)) {
            $this->errorMessage('Содержимое файла имеет не корректный формат.');
            return $this->redirect(['/page/index']);
        }
        $result = (new Page())->import(Json::decode($content));
        if ($result) {
            $this->successMessage('Страницы сайта обновлены.');
            return $this->redirect(['/page/index']);
        } else {
            $this->errorMessage('Произошла ошибка, попробуйте позже.');
            return $this->redirect(['/page/index']);
        }
    }

    /**
     * Удаление страницы сайта.
     * @return \yii\web\Response
     * @throws Exception
     */
    public function actionDelete()
    {
        $model = $this->getModel();
        if ($model->isNewRecord) {
            throw new Exception(404, 'Страница не найдена.');
        }
        $model->delete();
        return $this->redirect(['page/index']);
    }

    /**
     * @return Page
     * @throws Exception
     */
    private function getModel()
    {
        $id = (int)\Yii::$app->request->get('id', 0);
        /**
         * @var $model Page
         */
        if ($id) {
            $model = Page::findOne($id);
            if (!$model) {
                throw new Exception('Страницы не существует.');
            }
        } else {
            $model = new Page();
        }
        return $model;
    }
}