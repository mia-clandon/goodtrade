<?php /** @noinspection PhpMissingReturnTypeInspection */

/** @noinspection DuplicatedCode */

namespace common\libs\manticore;

use yii\base\NotSupportedException;
use yii\db\{Connection, Query, QueryBuilder as Builder};

/**
 * Class QueryBuilder
 * @package common\libs\manticore
 */
class QueryBuilder extends Builder
{
    private function getManticoreConnection(): Connection
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return \Yii::$app->get('manticore');
    }

    //Обновляет или добавляет запись в индекс.
    public function save(string $table, int $id, array $rows)
    {
        // есть ли запись в индексе ?
        $rt_row = (new Query())
            ->select(['id'])
            ->from($table)
            ->where(['id' => (int)$id])
            ->one($this->getManticoreConnection());

        if ($rt_row) {
            $query = $this->manticoreReplace($table, $rows);
        } else {
            $query = $this->manticoreInsert($table, $rows);
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->getManticoreConnection()->createCommand($query)->execute();
    }

    /**
     * Строит запрос UPDATE для SphinxQL
     * UPDATE works only with scalar attributes (int,float,bool,MVA). For strings and JSON you
     * need to use REPLACE.
     * @param string $table
     * @param array $rows
     * @param string $condition
     * @param $params
     * @return string
     * @throws NotSupportedException
     */
    public function manticoreUpdate(string $table, array $rows, string $condition, &$params): string
    {
        if (empty($rows)) {
            return '';
        }
        if (array_key_exists('id', $rows)) {
            unset($rows['id']);
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        $schema = $this->db->getSchema();
        $columns = array_keys($rows);
        foreach ($columns as $i => $name) {
            $columns[$i] = $schema->quoteColumnName($name);
        }
        $update = [];
        foreach ($rows as $column => $value) {
            $column = $schema->quoteColumnName($column);
            $value = $this->quoteValue($value);
            $update[] = $column . '=' . $value;
        }
        $sql = 'UPDATE ' . $schema->quoteTableName($table) . ' SET ' . implode(', ', $update);
        $where = $this->buildWhere($condition, $params);
        return $where === '' ? $sql : $sql . ' ' . $where;
    }

    //ВНИМАНИЕ! данный метод потрет все значения в записи индекса в случае нехватки их в $rows
    public function manticoreReplace(string $table, array $rows): string
    {
        $query = $this->manticoreInsert($table, $rows);
        if (!empty($query)) {
            return str_replace('INSERT INTO', 'REPLACE INTO', $query);
        }
        return '';
    }

    public function manticoreInsert(string $table, array $rows): string
    {
        if (empty($rows)) {
            return '';
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        $schema = $this->db->getSchema();
        $values = $vs2 = [];
        foreach ($rows as $value) {
            $vs = [];
            if (is_array($value)) {
                foreach ($value as $val) {
                    $vs[] = $this->quoteValue($val);
                }
                $values[] = '(' . implode(', ', $vs) . ')';
            } else {
                $vs2[] = $this->quoteValue($value);
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

    public function deleteById(string $index_name, int $id)
    {
        $id = (int)$id;
        /** @noinspection PhpUnhandledExceptionInspection */
        $schema = $this->db->getSchema();
        $params = [];
        return $this->delete($schema->quoteTableName($index_name), 'id=' . $id, $params);
    }

    public function delete($table, $condition, &$params)
    {
        $query = parent::delete($table, $condition, $params);
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->getManticoreConnection()->createCommand($query)->execute();
    }

    private function quoteValue(mixed $value): mixed
    {
        try {
            $schema = $this->db->getSchema();
        } catch (NotSupportedException $e) {
            return '';
        }
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
