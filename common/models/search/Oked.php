<?php

namespace common\models\search;

use common\models\base\Search;

/**
 * Class Oked
 * Класс для поиска ОКЭД в каталоге.
 * @package common\models\search
 * @author Артём Широких kowapssupport@gmail.com
 */
class Oked extends Search {

    #region Фильтры для поиска по ОКЕД.
    private $filter_key;
    private $filter_name;
    private $filter_not_exist_codes;
    #endregion;

    protected function getIndexName() {
        return \common\models\Oked::tableName();
    }

    public function getModel() {
        return \common\models\Oked::find();
    }

    protected function setFilters() {
        parent::setFilters();
        $query = $this->getQuery();
        if ($this->filter_key) {
            $query->andWhere(['key' => $this->filter_key]);
        }
        if ($this->filter_name) {
            $query->andMatch(['title' => $this->filter_name]);
        }
        if (!empty($this->filter_not_exist_codes)) {
            $query->andWhere(['NOT IN', 'key', $this->filter_not_exist_codes]);
        }
        return $this;
    }

    /**
     * @param integer $key
     * @return $this
     */
    public function setFilterKey($key) {
        $this->filter_key = (int)$key;
        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setFilterTitle($title) {
        $this->filter_name = (string)$title;
        return $this;
    }

    /**
     * @param array $existCodes
     * @return $this
     */
    public function setFilterNotExistCodes($existCodes) {
        $this->filter_not_exist_codes = $existCodes;
        return $this;
    }
}