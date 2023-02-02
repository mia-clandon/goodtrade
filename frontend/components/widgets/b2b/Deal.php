<?

namespace frontend\components\widgets\b2b;

use yii\base\Widget;

/**
 * Class Deal
 * @package frontend\components\widgets\b2b
 * @author Артём Широких kowapssupport@gmail.com
 */
class Deal extends Widget {

    public function run() {
        parent::run();
        $is_landing = \Yii::$app->controller->id === "site"
            && \Yii::$app->controller->action->id === "index";
        return $this->render('deal', [
            'is_landing' => $is_landing,
        ]);
    }
}