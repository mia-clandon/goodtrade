<?php

namespace common\libs\parser\vk;

use common\libs\parser\Parser as BaseParser;

/**
 * Class Base
 * @package common\libs\parser\vk
 * @author Артём Широких kowapssupport@gmail.com
 */
class Base extends BaseParser {

    /** @var int Количество найденных записей. */
    protected $count = null;

    /** @var null|string apiSection.apiMethod */
    protected $api_code = null;

    /** @var array Параметры запроса */
    private $params = [];

    /**
     * @param $count
     * @return Base
     */
    protected function setCount($count) {
        $this->count = (int)$count;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount() {
        return intval($this->count);
    }

    /**
     * Отдаёт контент какой есть
     * @return string
     */
    public function getContent() {
        $this->setFilterParam('v', 5.53); // версия api
        $this->generateUrlFromParams();
        return parent::getContent();
    }

    /**
     * Генерация url исходя из установленных фильтров.
     */
    public function generateUrlFromParams() {
        if (count($this->getFilterParams()) > 0) {
            $query = http_build_query($this->getFilterParams());
            if (!is_null($this->api_code)) {
                $this->setUrl('https://api.vk.com/method/'.$this->api_code.'?'. $query);
            }
        }
    }

    /**
     * @param $param
     * @param $value
     * @return $this
     */
    public function setFilterParam($param, $value) {
        $this->params[$param] = $value;
        return $this;
    }

    /**
     * @param $param
     * @return mixed|null
     */
    public function getFilterParam($param) {
        return isset($this->params[$param]) ?
            $this->params[$param] : null;
    }

    /**
     * @return array
     */
    public function getFilterParams() {
        return $this->params;
    }
}