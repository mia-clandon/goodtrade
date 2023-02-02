<?

namespace frontend\components\widgets;

use yii\base\Widget;

/**
 * Class Footer
 * @package frontend\components\widgets
 * @author Артём Широких kowapssupport@gmail.com
 */
class Footer extends Widget {

    public function run() {
        $controller_id = \Yii::$app->controller->id;
        if ($controller_id == 'compare' || $controller_id == 'favorite' ) {
            // для таблицы сравнения, избранных нет футера.
            return '';
        }
        return $this->render('footer');
    }
}