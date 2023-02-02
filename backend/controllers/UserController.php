<?php

namespace backend\controllers;

use backend\forms\user\Permissions;
use backend\forms\user\Update;
use common\libs\Env;
use common\models\firms\Firm;
use common\models\User;
use yii\base\Exception;

/**
 * Class UserController
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class UserController extends BaseController
{

    /**
     * @return string
     */
    public function actionIndex()
    {

        $model = new User();
        $data_provider = $model->search(\Yii::$app->request->get());

        return $this->render('index', [
            'model' => $model,
            'data_provider' => $data_provider,
        ]);
    }

    /**
     * Авторизация под пользователем.
     * @return \yii\web\Response
     * @throws Exception
     */
    public function actionLoginByUser()
    {
        $user_id = (int)\Yii::$app->request->get('id');
        if (!$user_id) {
            throw new Exception('Не передан параметр user_id.');
        }
        /** @var User $user */
        $user = User::findOne($user_id);
        if (!$user) {
            throw new Exception('Пользователь не найден в системе.');
        }
        $token = $user->updateAuthToken();
        if (false === $token) {
            return $this->redirect(['/user/index']);
        }
        // url до авторизации пользователя на frontend.
        $url = Env::i()->getFrontendUrl() . '/site/login-backend?' . http_build_query(['token' => $token,
                'user_id' => $user_id,]);
        return $this->redirect($url);
    }

    //Обновление пользователя.
    public function actionUpdate()
    {
        $model = $this->getModel();

        $form = (new Update())
            ->setModel($model)
            ->setPostMethod();

        if ($data = \Yii::$app->request->post()) {

            $form->setFormData($data);
            $form->validate();

            if ($form->save()) {

                if ($model->isNewRecord) {
                    return $this->refresh();
                } else {
                    $this->successMessage('Пользователь успешно сохранён !');
                    return $this->redirect(['user/update', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'form' => $form->render(),
        ]);
    }

    /**
     * Удаление пользователя.
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionDelete()
    {
        $model = $this->getModel();
        if ($model->isNewRecord) {
            throw new Exception(404, 'Пользователь не существует');
        }

        /**
         * @var Firm $firm
         */
        $firm = $model->getFirm()->one();

        if ($firm) {
            $this->errorMessage('К пользователю привязана организация, удалите оганизацию ' . $firm->getTitle());
            return $this->redirect(['user/index']);
        }

        $model->delete();
        return $this->redirect(['user/index']);
    }

    /**
     * Настройки прав доступа.
     * @throws \Exception
     */
    public function actionPermissions()
    {
        $form = new Permissions();
        $user_id = \Yii::$app->request->get('id', 0);
        if (!$user_id) {
            throw new Exception(404, 'Пользователь не найден.');
        }
        /** @var User|null $user */
        $user = User::findOne($user_id);
        if (null === $user) {
            throw new Exception(404, 'Пользователь не найден.');
        }
        $form->setUser($user);

        if ($data = \Yii::$app->request->post()) {

            $form->setFormData($data);
            $form->validate();

            if ($form->save()) {
                $this->successMessage('Права доступа обновлены.');
                return $this->redirect(['user/permissions', 'id' => $user_id]);
            } else {
                $this->refresh();
            }
        }
        return $this->render('permissions', [
            'form' => $form->render(),
        ]);
    }

    /**
     * @return User
     * @throws Exception
     */
    private function getModel(): User
    {
        $id = (int)\Yii::$app->request->get('id', 0);
        /**
         * @var $model User
         */
        if ($id) {
            $model = User::findOne($id);
            if (!$model) {
                throw new Exception('Пользователя не существует');
            }
        } else {
            $model = new User();
        }
        return $model;
    }
}