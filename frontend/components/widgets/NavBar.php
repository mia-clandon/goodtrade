<?

namespace frontend\components\widgets;

use common\models\firms\Firm;
use common\modules\image\helpers\Image as ImageHelper;
use frontend\controllers\BaseController;
use yii\base\Widget;

/**
 * Class NavBar
 * @package frontend\components\widgets
 * @author Артём Широких kowapssupport@gmail.com
 */
class NavBar extends Widget {

    /**
     * Метод рендерит хлебные крошки.
     * @return string
     */
    private function renderBreadcrumbs() {
        /** @var BaseController $controller */
        $controller = \Yii::$app->controller;
        if ($controller instanceof BaseController) {
            $breadcrumbs_object = $controller->getBreadcrumbs();
            return $breadcrumbs_object->render();
        }
        return '';
    }

    public function run() {
        return $this->render('nav-bar', [
            'is_guest'      => \Yii::$app->user->isGuest,
            'user'          => \Yii::$app->getUser()->getIdentity(),
            'avatar'        => $this->getAvatarImage(),
            'breadcrumbs'   => $this->renderBreadcrumbs(),
        ]);
    }

    /**
     * @return bool|string
     * @throws \yii\base\Exception
     */
    public function getAvatarImage() {
        if (\Yii::$app->user->isGuest) {
            return false;
        }
        $firm = Firm::get();
        $image = false;
        if (!empty($firm->image)) {
            $image = ImageHelper::i()->generateRelativeImageLink($firm->image, 50, 50, ImageHelper::RESIZE_MODE_AUTO);
        }
        return $image;
    }
}