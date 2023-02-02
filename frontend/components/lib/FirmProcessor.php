<?php

namespace frontend\components\lib;

use yii\base\Exception;

use common\libs\traits\Singleton;

use common\models\goods\search\Product as ProductFilter;

/**
 * Class FirmProcessor
 * todo FirmProcessor дублирует \common\models\firms\search\Firm - сделан для поиска..
 * @package frontend\components\lib
 * @author yerganat
 */
class FirmProcessor {
    use Singleton;

    /** @var null|integer */
    private $firm_id = null;

    /**
     * @param integer $firm_id
     * @return $this
     */
    public function setFirmId(int $firm_id) {
        $this->firm_id = (int)$firm_id;
        return $this;
    }

    /**
     * Список товаров компании.
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function getProductList(int $limit = 4): array {

        if (null === $this->firm_id) {
            throw new Exception(404, 'Компания не установлена.');
        }

        // поиск товаров организации.
        $product_search = (new ProductFilter())
            ->setFilterFirmId($this->firm_id)
        ;

        $product_list = $product_search->get()
            ->limit($limit)
            ->all();

        return $product_list;
    }
}