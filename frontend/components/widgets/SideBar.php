<?

namespace frontend\components\widgets;

use yii\base\Widget;

/**
 * Class SideBar
 * @package frontend\components\widgets
 * @author Артём Широких kowapssupport@gmail.com
 */
class SideBar extends Widget {

    public function run() {
        return $this->render('side-bar');
    }
}