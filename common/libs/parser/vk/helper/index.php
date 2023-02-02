<?php

namespace common\libs\parser;

use common\libs\parser\vk\Wall;
use common\libs\traits\Singleton;
use common\libs\VarDumper;

/**
 * Получение обьявлений в базу данных
 * Class VkGroupParseHelper
 * @package common\libs\parser
 */
class Index {

    use Singleton;

    public function parse() {

        // парсинг стены с группы http://vk.com/barahlo_32
        $posts = (new Wall())
            ->setFilterParam(Wall::DOMAIN, 'barahlo_32')
            ->setFilterParam(Wall::FILTER, 'all')
            ->setFilterParam(Wall::EXTENDED, 0)
            ->setFilterParam(Wall::COUNT, 3)

            //->get()
        ;

        // получить максимальную дату в базе по данной группе
        // из всех записей

        // если посты в posts > max_date тогда ложу ее в базу данных

        // исключая те посты в которых есть текст рекламы про барахолку

        // не пустой текст '', - должен исключать репосты.

        VarDumper::dump('1');
    }
}