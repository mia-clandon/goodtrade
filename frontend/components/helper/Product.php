<?php

namespace frontend\components\helper;

use common\libs\traits\Singleton;

/**
 * Class Product
 * @package frontend\components\helper
 * todo: удалить файл.
 * @author Артём Широких kowapssupport@gmail.com
 */
class Product {
    use Singleton;

    /**
     * Метод возвращает товары сгруппированные по организациям которые используют другие организации.
     * Используется для вывода организаций на странице авторизованного пользователя.
     * Блок: "n ПРОИЗВОДИТЕЛЕЙ ИСПОЛЬЗУЮТ ПОХОЖИЙ НА ВАШ ПРОДУКТ".
     * @return array
     */
    public function getSimilarProductList() {



        return [];
    }
}