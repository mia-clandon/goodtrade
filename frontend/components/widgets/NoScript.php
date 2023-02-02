<?

namespace frontend\components\widgets;

use yii\base\Widget;

/**
 * Class NoScript
 * @package frontend\components\widgets
 * @author Артём Широких kowapssupport@gmail.com
 */
class NoScript extends Widget {

    public function run() {
        return $this->render('no-script');
    }
}