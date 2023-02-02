<?php

namespace common\libs\parser\vk;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Database
 * @package common\libs\parser\vk
 * @author Артём Широких kowapssupport@gmail.com
 */
class Database extends Base {

    /** Идентификатор страны (https://new.vk.com/dev/country_codes). */
    const COUNTRY_ID = 'country_id';
    /** Получить все города. */
    const NEED_ALL = 'need_all';
    /** Сдвиг в базе. */
    const OFFSET = 'offset';
    /** Количество записей. */
    const COUNT = 'count';

    /**
     * @return array
     */
    public function getCities() {
        // навзание метода vk
        $this->methodName('getCities');
        // response
        $content = Json::decode($this->getContent());
        // города
        $items = ArrayHelper::getValue($content, 'response.items', []);
        // количество найденных записей.
        $this->setCount(ArrayHelper::getValue($content, 'response.count', 0));
        return $items;
    }

    private function setApiSection() {
        $this->api_code = strtolower(substr(strrchr(__CLASS__, "\\"), 1));
    }

    /**
     * @param $name
     */
    private function methodName($name) {
        $this->setApiSection();
        $this->api_code .= '.'.$name;
    }
}