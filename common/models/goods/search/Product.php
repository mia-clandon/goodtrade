<?php

namespace common\models\goods\search;

use common\models\base\Search;
use common\models\goods\Product as ProductModel;
use yii\base\Exception;
use yii\db\Expression;

/**
 * Class Product
 * @package common\models\goods\search
 * @author Артём Широких kowapssupport@gmail.com
 */
class Product extends Search {

    private const FILTER_PRICE_RANGE_FROM = 1;
    private const FILTER_PRICE_RANGE_TO = 2;

    #region Фильтры для поиска материалов.
    private $filter_title;
    private $filter_categories;
    private $filter_firm_id;
    private $filter_not_firm_id;
    private $filter_status;
    private $filter_user_id;
    private $filter_delivery_terms;
    private $filter_vocabularies = [];
    private $filter_terms = [];
    private $filter_price_range = [];
    private $filter_with_vat;
    private $filter_capacities_from;
    private $filter_capacities_to;
    #endregion

    protected function getIndexName() {
        return ProductModel::tableName();
    }

    public function getModel() {
        return ProductModel::find();
    }

    /**
     * todo: use bindParam()
     * @return $this
     */
    protected function setFilters() {
        parent::setFilters();
        $query = $this->getQuery();

        // фильтр по категориям.
        if ($this->filter_categories) {
            $query->andWhere(['categories' => $this->filter_categories]);
        }
        // фильтр по названию товара.
        if ($this->filter_title) {
            $query->andMatch(['title' => (string)$this->filter_title]);
        }
        // фильтр по идентификатору организации.
        if ($this->filter_firm_id) {
            $query->andWhere(['firm_id' => (int)$this->filter_firm_id]);
        }
        if (null !== $this->filter_not_firm_id) {
            $query->andWhere(['<>', 'firm_id', (int)$this->filter_not_firm_id]);
        }
        // фильтр по статусу.
        if ($this->filter_status) {
            $query->andWhere(['status' => (int)$this->filter_status]);
        }
        // условия доставки.
        if (null !== $this->filter_delivery_terms) {
            $query->andWhere(['delivery_terms' => $this->filter_delivery_terms]);
        }
        // фильтр по диапазону цен товара.
        if (!empty($this->filter_price_range[self::FILTER_PRICE_RANGE_FROM]) ?? null) {
            $from = $this->filter_price_range[self::FILTER_PRICE_RANGE_FROM];
            $query->andWhere("price >= $from");
        }
        if (!empty($this->filter_price_range[self::FILTER_PRICE_RANGE_TO]) ?? null) {
            $to = $this->filter_price_range[self::FILTER_PRICE_RANGE_TO];
            $query->andWhere("price <= $to");
        }
        // с НДС ?
        if (null !== $this->filter_with_vat && is_bool($this->filter_with_vat)) {
            $query->andWhere(['with_vat' => $this->filter_with_vat]);
        }
        // мощности от - до.
        if (null !== $this->filter_capacities_from) {
            $query->andWhere(['>=', 'capacities_from', $this->filter_capacities_from]);
        }
        if (null !== $this->filter_capacities_to) {
            $query->andWhere(['<=', 'capacities_to', $this->filter_capacities_to]);
        }
        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setFilterTitle(string $title) {
        if (!empty(trim($title))) {
            $this->filter_title = $title;
        }
        return $this;
    }

    /**
     * Фильтр по диапазону цен товара.
     * @param float|null $price_from
     * @param float|null $price_to
     * @return $this
     */
    public function setFilterPriceRange(float $price_from = null, float $price_to = null) {
        $this->filter_price_range[self::FILTER_PRICE_RANGE_FROM] = $price_from;
        $this->filter_price_range[self::FILTER_PRICE_RANGE_TO] = $price_to;
        return $this;
    }

    /**
     * @param float $price_from
     * @return $this
     */
    public function setFilterPriceRangeFrom(float $price_from) {
        $this->filter_price_range[self::FILTER_PRICE_RANGE_FROM] = $price_from;
        return $this;
    }

    /**
     * @param float $price_to
     * @return $this
     */
    public function setFilterPriceRangeTo(float $price_to) {
        $this->filter_price_range[self::FILTER_PRICE_RANGE_TO] = $price_to;
        return $this;
    }

    /**
     * @param bool $with_vat
     * @return $this
     */
    public function setFilterPriceVat(bool $with_vat) {
        $this->filter_with_vat = $with_vat;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFilterCapacitiesFrom(int $value) {
        $this->filter_capacities_from = $value;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFilterCapacitiesTo(int $value) {
        $this->filter_capacities_to = $value;
        return $this;
    }

    /**
     * Фильтр поиска по категориям.
     * @param array $categories
     * @return $this
     */
    public function setFilterCategories(array $categories) {
        $categories = array_map ('intval', $categories);
        $this->filter_categories = $categories;
        return $this;
    }

    /**
     * Фильтр по товарам определённой организации.
     * @param integer $firm_id
     * @return $this
     */
    public function setFilterFirmId(int $firm_id) {
        $this->filter_firm_id = (int)$firm_id;
        return $this;
    }

    /**
     * @param integer $firm_id
     * @return $this
     */
    public function setFilterNotFirmId(int $firm_id) {
        $this->filter_not_firm_id = (int)$firm_id;
        return $this;
    }

    /**
     * Фильтр по статусу товара.
     * @param integer $status
     * @return $this
     */
    public function setFilterStatus(int $status) {
        $this->filter_status = (int)$status;
        return $this;
    }

    /**
     * @param array $terms
     * @return $this
     */
    public function setFilterDeliveryTerms(array $terms) {
        $this->filter_delivery_terms = $terms;
        return $this;
    }

    /**
     * @param int $term_id
     * @return $this
     */
    public function addFilterDeliveryTerm(int $term_id) {
        $this->filter_delivery_terms[] = $term_id;
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     * @throws Exception
     */
    public function setFilterParams(array $params) {
        $filter_prefix = 'filter_';
        foreach ($params as $param_name => $param_value) {
            if (!property_exists($this, $filter_prefix. $param_name)) {
                throw new Exception('Не найдено свойство фильтра.');
            }
            if ($param_name == 'categories') {
                $param_value = [(int)$param_value];
            }
            $this->{$filter_prefix.$param_name} = $param_value;
        }
        return $this;
    }

    /**
     * Возвращает массив с количеством товаров и компании в категориях
     * @param array $category_ids
     * @return array
     */
    public function getProductFirmCountBy(array $category_ids) {

        // организации с найденными товарами.
        $product_filter = (new Product())
            ->select([
                new Expression('count(*) as `count`'),
                new Expression('GROUP_CONCAT(firm_id) as firm_ids'),
            ])
            ->setFilterStatus(ProductModel::STATUS_PRODUCT_ACTIVE)
            ->setFilterCategories($category_ids)
        ;

        $counters = $product_filter->find();

        return [
            'product_count' => $counters[0]['count'] ?? 0,
            'firm_count' => 0
            //'firm_count' => count(array_unique(array_diff(explode(',', $counters[0]['firm_ids']), [null, 0])))
        ];
    }
}