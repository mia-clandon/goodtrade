<?php

namespace backend\controllers;

use backend\forms\firm\Update;
use common\models\firms\Firm;
use common\models\User;
use yii\db\Exception;
use yii\web\Response;

/**
 * Class FirmController
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class FirmController extends BaseController
{

    /**
     * Список организаций площадки.
     */
    public function actionIndex()
    {

        $model = new Firm();
        $data_provider = $model->search(\Yii::$app->request->get());

        return $this->render('index', [
            'model' => $model,
            'data_provider' => $data_provider,
        ]);
    }

    /**
     * Обновление организации.
     * @return string
     * @throws Exception
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

            if ($form->save()) {
                $this->successMessage('Организация успешно сохранилась !');
                return $this->redirect(['firm/update', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'form' => $form->render(),
            'model' => $model,
        ]);
    }

    /**
     * TODO-f - перетащить потом в api.
     * @return array
     */
    public function actionSearchUser()
    {
        $q = \Yii::$app->request->get('q');
        $this->layout = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if ($q) {
            $user = User::find();
            // поиск по id.
            if (is_numeric($q)) {
                $user = $user->where(['id' => $q]);
            } else {
                $user = $user->where(['like', 'email', $q]);
            }

            $users = $user->select(['id', 'email'])->asArray()->all();
            return $users;
        } else {
            return [];
        }
    }

    /**
     * Удаление организации.
     * @throws Exception
     */
    public function actionDelete()
    {
        $model = $this->getModel();
        if ($model->isNewRecord) {
            throw new Exception(404, 'Организации не существует');
        }
        if ($model->useFirmInProduct()) {
            $this->errorMessage('Данная организация используется в товарах. Смените организацию у товара и попробуйте удалить еще раз.');
            return $this->redirect(['firm/index']);
        }
        $model->delete();
        return $this->redirect(['firm/index']);
    }

    /**
     * Удаление фотографии организации.
     * @throws \yii\base\Exception
     */
    public function actionRemoveImage()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new \yii\base\Exception('Страница не найдена.');
        }
        $id = \Yii::$app->request->post('entity_id');
        if (!$id) {
            throw new \yii\base\Exception('Страница не найдена.');
        }
        /** @var Firm $model */
        $model = Firm::findOne($id);
        $model->clearImage();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => $model->save()];
    }

    /**
     * @return Response
     */
    public function actionUpdateIndex()
    {
        $model = $this->getModel();
        $model->updateIndex();
        \Yii::$app->getSession()->setFlash(BaseController::FLASH_MESSAGE_SUCCESS, 'Индекс обновился');
        return $this->redirect(['firm/index']);
    }

    /**
     * @return Firm
     * @throws Exception
     */
    private function getModel()
    {
        $id = (int)\Yii::$app->request->get('id', \Yii::$app->request->post('id', 0));
        /**
         * @var $model Firm
         */
        if ($id) {
            $model = Firm::findOne($id);
            if (!$model) {
                throw new Exception('Организации не существует');
            }
        } else {
            $model = new Firm();
        }
        return $model;
    }
}