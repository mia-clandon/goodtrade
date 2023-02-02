<?

namespace frontend\components\widgets\b2b;

use frontend\controllers\BaseController;
use frontend\controllers\SiteController;

/**
 * Class NavBar
 * @package frontend\components\b2b\widgets
 * @author yerganat
 */
class NavBar extends \frontend\components\widgets\NavBar {

    /** @var bool - главная страница ? */
    private $is_landing = false;

    /**
     * Метод рендерит хлебные крошки.
     * @return string
     */
    private function renderBreadcrumbs() {
        /** @var BaseController $controller */
        $controller = \Yii::$app->controller;
        if ($controller instanceof BaseController) {
            $breadcrumbs_object = $controller->getBreadcrumbsB2B();
            return $breadcrumbs_object->render();
        }
        return '';
    }

    public function run() {
        /** @var BaseController $controller */
        $controller = \Yii::$app->controller;
        if ($controller instanceof SiteController) {
            $this->is_landing = true;
        }
        $search_form = $controller->getSearchForm();
        return $this->render('nav-bar', [
            'is_landing'    => $this->is_landing,
            'is_guest'      => \Yii::$app->user->isGuest,
            'user'          => \Yii::$app->getUser()->getIdentity(),
            'avatar'        => $this->getAvatarImage(),
            'breadcrumbs'   => $this->renderBreadcrumbs(),
            'controller'    => $controller->id,
            'search_form'   => $search_form->render(),
        ]);
    }
}