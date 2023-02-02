<?

namespace frontend\components\widgets;

use yii\base\Widget;

/**
 * Заказ обратного звонка у организации.
 * Class Callback
 * @package frontend\components\widgets
 * @author Артём Широких kowapssupport@gmail.com
 */
class Callback extends Widget  {

    public function run() {
        return $this->render('callback');
    }
}