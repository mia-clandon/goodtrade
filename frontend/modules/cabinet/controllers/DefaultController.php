<?php

namespace frontend\modules\cabinet\controllers;

use yii\filters\AccessControl;

use common\libs\Env;

use frontend\controllers\BaseController;
use frontend\assets\CabinetAssets;

/**
 * Class DefaultController
 * @package frontend\modules\cabinet\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class DefaultController extends BaseController {

    public $layout = 'main';

    protected $active_menu = null;

    public function beforeAction($action) {
        if (!Env::i()->isProd()) {
            \Yii::$app->view->registerCss('
                code {
                    position: absolute;
                    z-index: 1111;
                    display: block;
                    background: white;
                }
            ');
        }

        // Зависимости кабинета.
        CabinetAssets::register(\Yii::$app->getView());

        // Ссылка на кабинет в хлебных крошках.
        $this->getBreadcrumbs()->addBreadcrumbsLink(
            \Yii::$app->urlManager->createUrl(['/cabinet']),
            'Кабинет'
        );

        return parent::beforeAction($action);
    }

    public function behaviors() {
        //if (Env::i()->isProduction()) {
            return [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'], // разрешаю все авторизованным.
                        ],
                    ],
                ]
            ];
//        }
//        else {
//            return [];
//        }
    }

    /**
     * @param string $menu
     * @return $this
     */
    public function setActiveMenu($menu) {
        $this->active_menu = (string)$menu;
        return $this;
    }
}