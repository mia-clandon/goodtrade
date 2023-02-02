<?php

namespace common\models\firms\search;

use yii\data\Pagination;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

use common\models\goods\Product;
use common\models\base\Search;
use common\models\firms\Firm as FirmModel;
use common\models\goods\search\Product as ProductSearch;

/**
 * Class Firm
 * Класс для поиска организаций по различным параметрам.
 * @package common\models\goods\search
 * @author Артём Широких kowapssupport@gmail.com
 */
class Firm extends Search {

    /** @var bool */
    private $with_first_firm = false;
    /** @var null|FirmModel */
    private $first_firm = null;
    /** @var int */
    private $current_page = 0;

    protected function getIndexName() {
        return FirmModel::tableName();
    }

    public function getModel() {
        return FirmModel::find();
    }

    /**
     * @param int $current_page
     * @return $this
     */
    public function setCurrentPage(int $current_page) {
        $this->current_page = $current_page;
        return $this;
    }

    /**
     * todo: порефакторить напроч.
     * Поиск организаций по товарам.
     * @return FirmModel[]
     */
    public function findFirmsByProduct() {

        // организации с найденными товарами.
        $product_filter = (new ProductSearch())
            ->select([
                'firm_id',
                new Expression('count(*) as `count`'),
                new Expression('GROUP_CONCAT(id) as product_list'),
            ])
            ->setFilterTitle($this->query_string)
            ->setFilterStatus(Product::STATUS_PRODUCT_ACTIVE)
            ->setGroupBy(['firm_id'])
        ;
        $count = $product_filter->count();
        $this->setCount($count);

        if (!$count) {
            return [];
        }

        // обработка sphinx результата.
        $found_firms = $product_filter->find();
        $found = [];
        foreach ($found_firms as $found_firm) {
            $key = (int)$found_firm['firm_id'];
            $found[$key] = $found_firm;
            $found[$key]['id'] = $found_firm['firm_id'];
        }

        $pagination = new Pagination(['totalCount' => $count]);
        $pagination->setPageSize($this->per_page_count);
        $pagination->setPage($this->current_page);

        /** @var FirmModel[] $found_firms */
        $found_firms = $this->get(array_keys($found))
            ->limit($pagination->limit)
            ->offset($pagination->offset)
            ->all();

        $this->setPagination($pagination);

        /** @var FirmModel $found_firm */
        foreach ($found_firms as $found_firm) {
            $product_ids = ArrayHelper::getValue($found, $found_firm->id.'.product_list', '');
            $found_firm->setProductIds(explode(',', $product_ids));
        }

        if ($this->with_first_firm) {
            // получение первой записи.
            $first_firm = ArrayHelper::getValue($found_firms, 0, null);
            $this->setFirstFirm($first_firm);
            unset($found_firms[0]);
            $found_firms = array_values($found_firms);
        }

        if ($this->chunk_part_size > 0) {
            // разделение записей на части.
            $found_firms = array_chunk($found_firms, $this->chunk_part_size);
        }

        return $found_firms;
    }

    /**
     * @param boolean $with_first_firm
     * @return $this;
     */
    public function setWithFirstFirm($with_first_firm = true) {
        $this->with_first_firm = (bool)$with_first_firm;
        return $this;
    }

    /**
     * @param FirmModel|null $firm
     * @return $this
     */
    public function setFirstFirm($firm) {
        $this->first_firm = $firm;
        return $this;
    }

    /**
     * @return FirmModel|null
     */
    public function getFirstFirm() {
        return $this->first_firm;
    }
}