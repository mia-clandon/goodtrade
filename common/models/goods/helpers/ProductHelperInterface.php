<?php

namespace common\models\goods\helpers;

use common\models\goods\Product;

/**
 * Interface ProductHelperInterface
 * Общий интерфейс для хелперов модели товаров.
 * @package common\models\goods\helpers
 * @author Артём Широких kowapssupport@gmail.com
 */
interface ProductHelperInterface {

    /**
     * @param Product $model
     * @return $this;
     */
    public function setProductModel(Product $model);

    /**
     * @return Product
     */
    public function getProductModel();
}