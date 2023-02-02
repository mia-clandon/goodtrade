<?

namespace frontend\components\widgets\b2b;

/**
 * Class Compare
 * Виджет отображающий категории товаров и товары для сравнения в шапке сайта.
 * @package frontend\components\widgets\b2b
 * @author yerganat
 */
class Compare extends \frontend\components\widgets\Compare {

    public function run() {
        $is_landing = false;
        $controller_id = \Yii::$app->controller->id;
        if ($controller_id == 'site') {
            // для главной страницы.
            $is_landing = true;
        }

        $compare_data = $this->getData();
        return $this->render('compare', [
            'compare_data'  => $compare_data,
            'compare_count' => count($compare_data),
            'is_landing'    => $is_landing,
        ]);
    }
}