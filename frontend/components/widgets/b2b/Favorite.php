<?

namespace frontend\components\widgets\b2b;

/**
 * Class Favorite
 * Виджет отображающий компании и товары избранного.
 * @package frontend\components\widgets\b2b
 * @author yerganat
 */
class Favorite extends \frontend\components\widgets\Favorite {

    public function run() {
        $is_landing = false;
        $controller_id = \Yii::$app->controller->id;
        if ($controller_id == 'site') {
            // для главной страницы.
            $is_landing = true;
        }

        $favorite_data = $this->getData();
        return $this->render('favorite', [
            'favorite_data'  => $favorite_data,
            'favorite_count' => count($favorite_data),
            'is_landing'    => $is_landing,
        ]);
    }
}