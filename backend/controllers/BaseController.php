<?php

namespace backend\controllers;

use yii;
use yii\web\Controller;
use common\libs\helpers\Permission;

/**
 * Site controller
 * @author Артём Широких kowapssupport@gmail.com
 */
class BaseController extends Controller {

    /** @var bool - todo: временно. */
    public $enableCsrfValidation = false;

    const FLASH_MESSAGE_SUCCESS = 'message_success';
    const FLASH_MESSAGE_ERROR = 'message_error';

    private $left_menu_hidden = false;

    /**
     * @return array
     */
    public function behaviors() {

        // access settings
        $access = [
            'class' => yii\filters\AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => Permission::i()->getAdminGroupRoles(),
                ],
                [
                    'allow' => true,
                    'actions' => ['sign-in',], // всем страницу авторизации.
                    'roles' => [],
                ],
            ],
        ];

        return ['access' => $access,];

//        return (YII_ENV == 'prod')
//            ? ['access' => $access,]
//            : [];
    }

    /**
     * Метод устанавливает сообщение со статусом "success".
     * @param string $message
     * @return $this
     */
    protected function successMessage($message) {
        \Yii::$app->getSession()->setFlash(BaseController::FLASH_MESSAGE_SUCCESS, $message);
        return $this;
    }

    /**
     * Метод устанавливает сообщение со статусом "error".
     * @param string $message
     * @return $this
     */
    protected function errorMessage($message) {
        \Yii::$app->getSession()->setFlash(BaseController::FLASH_MESSAGE_ERROR, $message);
        return $this;
    }

    public function actions() {
        return [
            'error' => ['class' => 'yii\web\ErrorAction'],
        ];
    }

    /**
     * @return static
     */
    public function hideLeftMenu() {
        $this->left_menu_hidden = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowLeftMenu() {
        return (bool)!$this->left_menu_hidden;
    }
}