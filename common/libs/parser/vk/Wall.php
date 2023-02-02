<?php

namespace common\libs\parser\vk;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Wall
 * @package common\libs\parser\vk
 * @author Артём Широких kowapssupport@gmail.com
 */
class Wall extends Base {

    /** короткий адрес пользователя или сообщества. */
    const DOMAIN = 'domain';
    /** suggests, postponed, owner, others, all */
    const FILTER = 'filter';
    /** в ответе будут возвращены дополнительные поля profiles и groups, содержащие информацию о пользователях и сообществах. */
    const EXTENDED = 'extended';
    /** количество записей, которое необходимо получить. Максимальное значение: 100.  */
    const COUNT = 'count';

    /**
     * Возвращает список записей со стены пользователя или сообщества.
     * Параметры:
     * owner_id - идентификатор пользователя или сообщества,
     * со стены которого необходимо получить записи (по умолчанию — текущий пользователь).
     * domain - короткий адрес пользователя или сообщества.
     * offset - смещение, необходимое для выборки определенного подмножества записей.
     * count - количество записей, которое необходимо получить. Максимальное значение: 100.
     * filter - определяет, какие типы записей на стене необходимо получить. Возможные значения:
                suggests — предложенные записи на стене сообщества (доступно только при вызове с передачей access_token);
                postponed — отложенные записи (доступно только при вызове с передачей access_token);
                owner — записи владельца стены;
                others — записи не от владельца стены;
                all — все записи на стене (owner + others).
                По умолчанию: all.
     * extended 1 — в ответе будут возвращены дополнительные поля profiles и groups, содержащие информацию о пользователях и сообществах.
     * По умолчанию: 0.
     * @return array
     */
    public function get() {
        // навзание метода vk
        $this->methodName('get');
        // response
        $content = Json::decode($this->getContent());
        // элементы стены.
        $items = ArrayHelper::getValue($content, 'response.items', []);
        return $items;
    }

    /**
     * set section
     */
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