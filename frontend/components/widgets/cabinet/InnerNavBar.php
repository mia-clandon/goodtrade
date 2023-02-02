<?php

namespace frontend\components\widgets\cabinet;

use frontend\modules\cabinet\controllers\DefaultController;
use yii\base\Widget;

/**
 * Class InnerNavBar
 * @package frontend\components\widgets\cabinet
 * @author Артём Широких kowapssupport@gmail.com
 */
class InnerNavBar extends Widget {

    public function run() {
        /** @var DefaultController $controller */
        $controller = \Yii::$app->controller;
        $controller_name = $controller->id;
        $action_name = $controller->action->id;
        return $this->render('inner-nav-bar', [
            'controller_name' => $controller_name,
            'action_name' => $action_name,
        ]);
    }
}