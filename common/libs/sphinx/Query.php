<?php

namespace common\libs\sphinx;

use yii\db\Expression;
use yii\db\Query as QueryBase;

use common\libs\StringBuilder;
use common\libs\Transliterate;
use common\libs\VarDumper;

/**
 * Class Query
 * @package common\libs\sphinx
 * @author Артём Широких kowapssupport@gmail.com
 */
class Query extends QueryBase {

    public $where;

    /** @var bool */
    private $use_match_transliterate = false;

    /**
     * Использовать транслитерацию при поиске Match
     * Внимание, вызывать ф-ю перед использованием (and|or)MATCH
     * @param $use
     * @return Query
     */
    public function setMatchTransliterate($use) {
        $this->use_match_transliterate = (bool)$use;
        return $this;
    }

    /**
     * @param array $column_values ['column' => 'values']
     * @param array $column_values
     * @return Query
     */
    public function match(array $column_values) {
        $match = $this->buildMatch($column_values);
        if (!$match) {
            return $this;
        }
        $this->where(new Expression("MATCH(:query)", [
            ':query' => $match,
        ]));
        return $this;
    }

    /**
     * @param array $column_values
     * @return $this
     * @see [[match()]]
     */
    public function andMatch(array $column_values) {
        $match = $this->buildMatch($column_values);
        if (!$match) {
            return $this;
        }
        $this->andWhere(new Expression("MATCH(:query)", [
            ':query' => $match,
        ]));
        return $this;
    }

    /**
     * @param array $column_values
     * @return $this
     * @see [[match()]]
     */
    public function orMatch(array $column_values) {
        $match = $this->buildMatch($column_values);
        if (!$match) {
            return $this;
        }
        $this->orWhere(new Expression("MATCH(:query)", [
            ':query' => $match,
        ]));
        return $this;
    }

    /**
     * @param array $column_values
     * @return string
     */
    private function buildMatch(array $column_values) {
        if (!$column_values) {
            return '';
        }
        $query_string = new StringBuilder();
        foreach ($column_values as $column => $value) {
            if (empty($value)) continue;
            /*
            // если значение является числом - ищем через where
            if (is_numeric($value)) {
                $this->where($column.'='.':'.$column, [':'.$column => (int)$value]);
            }
            else {}
            */
            $column = str_replace('@', '', $column);
            if ($this->use_match_transliterate) {
                $value = '('.$value.')|('.Transliterate::getInstance()->get($value).')';
            }
            $query_string
                ->add('@')
                ->add($column.' ')
                ->add($value.' ')
            ;
        }
        return rtrim($query_string->get(), ' ');
    }

    /**
     * Очистка where условия.
     * @return $this
     */
    public function clearWhere() {
        $this->where = null;
        return $this;
    }

    /**
     * Добавил возможность просматривать запрос.
     * @param null $db
     * @param bool $debug
     * @return array
     */
    public function all($db = null, $debug = false) {
        if ($debug) {
            $clone = clone $this;
            $sql = $clone->createCommand()->getSql();
            VarDumper::dump($sql);
        }
        return [];
    }

    public function count($q = '*', $db = null, $debug = false) {
        if ($debug) {
            $clone = clone $this;
            $sql = $clone->createCommand()->getSql();
            VarDumper::dump($sql);
        }
        return parent::count($q, $db); // TODO: Change the autogenerated stub
    }
}