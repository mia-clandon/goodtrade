<?php

namespace common\models\goods\helpers;

use common\models\goods\Product;
use yii\db\Exception;

/**
 * Class Base
 * @package common\models\goods\helpers
 * @author Артём Широких kowapssupport@gmail.com
 */
class Base implements ProductHelperInterface {

    /** @var null|Product */
    private $product_model = null;

    public function setProductModel(Product $model) {
        $this->product_model = $model;
        return $this;
    }

    public function getProductModel() {
        if (null === $this->product_model) {
            throw new Exception('Модель товаров не установлена.');
        }
        return $this->product_model;
    }
}