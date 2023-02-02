<?php

namespace common\libs\sphinx;

use yii\db\Query;
use yii\db\QueryBuilder as Builder;

/**
 * Class StringHelper
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class QueryBuilder extends Builder {

    /**
     * @return null|\yii\db\Connection
     * @throws \yii\base\InvalidConfigException
     */
    private function getSphinxConnection() {
        return \Yii::$app->get('sphinx');
    }

    /**
     * Обновляет или добавляет запись в индекс.
     * @param $table
     * @param $id
     * @param array $rows
     * @return int
     */
    public function sphinxSave($table, $id, array $rows) {

        // есть ли запись в Sphinx ?
        $rt_row = (new Query())
            ->select(['id'])
            ->from($table)
            ->where(['id' => (int)$id])
            ->one($this->getSphinxConnection())
        ;

        if ($rt_row) {
            $query = $this->sphinxReplace($table, $rows);
        }
        else {
            $query = $this->sphinxInsert($table, $rows);
        }
        return $this->getSphinxConnection()->createCommand($query)->execute();
    }

    /**
     * Строит запрос UPDATE для SphinxQL
     * UPDATE works only with scalar attributes (int,float,bool,MVA). For strings and JSON you
     * need to use REPLACE.
     * @param $table
     * @param array $rows
     * @param $condition
     * @param $params
     * @return string
     */
    public function sphinxUpdate($table, array $rows, $condition, &$params) {
        if (empty($rows)) {
            return '';
        }
        if (array_key_exists('id', $rows)) {
            unset($rows['id']);
        }
        $schema = $this->db->getSchema();
        $columns = array_keys($rows);
        foreach ($columns as $i => $name) {
            $columns[$i] = $schema->quoteColumnName($name);
        }
        $update = [];
        foreach ($rows as $column => $value) {
            $column = $schema->quoteColumnName($column);
            $value = $this->sphinxQuoteValue($value);
            $update[] = $column . '=' . $value;
        }
        $sql = 'UPDATE ' . $schema->quoteTableName($table) . ' SET ' . implode(', ', $update);
        $where = $this->buildWhere($condition, $params);
        return $where === '' ? $sql : $sql . ' ' . $where;
    }

    /**
     * ВНИМАНИЕ! данный метод потрет все значения в записи индекса в случае нехватки их в $rows
     * @param $table
     * @param array $rows
     * @return mixed|string
     */
    public function sphinxReplace($table, array $rows) {
        $query = $this->sphinxInsert($table, $rows);
        if (!empty($query)) {
            return str_replace('INSERT INTO', 'REPLACE INTO', $query);
        }
        return '';
    }

    /**
     * insert, batch insert - специально для SphinxQL
     * @param $table
     * @param array $rows
     * @return string
     */
    public function sphinxInsert($table, array $rows) {
        if (empty($rows)) {
            return '';
        }
        $schema = $this->db->getSchema();
        $values = $vs2 = [];
        foreach ($rows as $value) {
            $vs = [];
            if (is_array($value)) {
                foreach ($value as $val) {
                    $vs[] = $this->sphinxQuoteValue($val);
                }
                $values[] = '(' . implode(', ', $vs) . ')';
            }
            else {
                $vs2[] = $this->sphinxQuoteValue($value);
            }
        }
        if (!empty($vs2)) {
            $values[] = '(' . implode(', ', $vs2) . ')';
        }
        $columns = array_keys($rows);
        foreach ($columns as $i => $name) {
            $columns[$i] = $schema->quoteColumnName($name);
        }
        return 'INSERT INTO ' . $schema->quoteTableName($table) . ' (' . implode(', ', $columns) . ') VALUES ' . implode(', ', $values);
    }

    /**
     * @param string $index_name
     * @param integer $id
     * @return int
     */
    public function deleteById($index_name, $id) {
        $id = (int)$id;
        $schema = $this->db->getSchema();
        $params = [];
        return $this->delete($schema->quoteTableName($index_name), 'id='. $id, $params);
    }

    /**
     * @param string $table
     * @param array|string $condition
     * @param array $params
     * @return int
     */
    public function delete($table, $condition, &$params) {
        $query = parent::delete($table, $condition, $params);
        return $this->getSphinxConnection()->createCommand($query)->execute();
    }

    /**
     * @param $value
     * @return int|string
     */
    private function sphinxQuoteValue($value) {
        $schema = $this->db->getSchema();
        if (is_string($value)) {
            $value = $schema->quoteValue($value);
        } elseif ($value === false) {
            $value = 0;
        } elseif ($value === null) {
            $value = 'NULL';
        }
        return $value;
    }
}