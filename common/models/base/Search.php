<?php

namespace common\models\base;

use yii\data\Pagination;
use yii\sphinx\Connection;
use yii\base\Exception;
use yii\db\{ActiveQuery, Expression};
use yii\helpers\ArrayHelper;

use common\libs\sphinx\Query;

/**
 * Class Search
 * TODO: chunk_part_size, per_page_count, pagination
 * Базовый класс для фильтров поиска Sphinx.
 * @package common\models\base
 * @author Артём Широких kowapssupport@gmail.com
 */
class Search
{

    /** @var array - параметры выборки. */
    protected $select = [];
    /** @var null|string - строка поискового запроса. */
    protected $query_string;
    /** @var null|Query - объект запроса. */
    protected $query = null;
    /** @var integer - лимит поиска sphinx. */
    protected $limit = 1000;
    /** @var  string - сортировка sphinx. */
    protected $order_by = 'weight DESC';
    /** @var null|array - группировка по колонке. */
    protected $group_by = null;
    /** @var int - количество найденных записей. */
    protected $count = null;

    # стандартные фильтры.
    protected $filter_not_in_id = [];
    protected $filter_in_id = [];

    #region - дополнительные параметры для выдачи результатов.
    /**
     * @var integer - размер партии результатов
     * пример при значении (2): [
     *    [
     *      1 => ...
     *      2 => ...
     *    ],
     *    [
     *      3 => ...
     *      4 => ...
     *    ]
     * ]
     */
    protected $chunk_part_size = 0;
    /** @var int - количество записей на страницу. */
    protected $per_page_count = 0;
    /** @var null|Pagination - пагинатор. */
    protected $pagination = null;
    #endregion

    /** @var array */
    private $found_id_list = null;

    /**
     * Название индекса.
     * @return string
     */
    protected function getIndexName()
    {
        return '';
    }

    /**
     * @return ActiveQuery
     * @throws Exception
     */
    public function getModel()
    {
        throw new Exception('Переопределите метод ' . __METHOD__);
    }

    /**
     * Устанавливает фильтры для поиска в индексе.
     * @return $this
     */
    protected function setFilters()
    {
        $query = $this->getQuery()
            ->clearWhere();
        if ($this->group_by) {
            $query->groupBy($this->group_by);
        }
        // not in id.
        if (!empty($this->filter_not_in_id)) {
            $query->andWhere(['NOT IN', 'id', $this->filter_not_in_id]);
        }
        // in id.
        if (!empty($this->filter_in_id)) {
            $query->andWhere(['IN', 'id', $this->filter_in_id]);
        }
        return $this;
    }

    /**
     * @return Query
     */
    protected function getQuery()
    {
        if (is_null($this->query)) {
            $this->query = (new Query())
                ->select(array_merge(
                    ['id', new Expression('weight() as weight')],
                    $this->select
                ))
                ->from($this->getIndexName());
        }
        return $this->query;
    }

    /**
     * Дополнительная выборка.
     * @param array $select
     * @return $this
     */
    public function select(array $select)
    {
        $this->select = array_merge($this->select, $select);
        return $this;
    }

    /**
     * Метод ищет @param array $id_list
     * @return ActiveQuery
     * @see: \yii\db\ActiveRecord::find в индексе Sphinx и отдаёт.
     * ActiveQuery материалов.
     */
    public function get(array $id_list = [])
    {

        if (empty($id_list)) {

            $found = $this->find();

            $entity_list = (empty($found)) ? [['id' => 0]] : $found;
            $id_list = ArrayHelper::getColumn($entity_list, 'id', [0]);
        }

        $entity_list = array_map('intval', $id_list);

        $entity_list = $this->getModel()
            ->where(['id' => $entity_list])
            ->orderBy(new Expression("FIND_IN_SET(id, '" . implode(',', $entity_list) . "')"));

        return $entity_list;
    }

    /**
     * Возвращает (подсчитывает) количество найденных записей.
     * @return int
     */
    public function count()
    {
        /*
        $this->find();

        /** @var Connection $sphinx */
//        $sphinx = \Yii::$app->get('sphinx');
//        $rows = $sphinx->createCommand('SHOW META')->queryAll();
//
//        foreach ($rows as $row) {
//            $variable_name = ArrayHelper::getValue($row, 'Variable_name');
//            if ($variable_name == 'total_found') {
//                $count = (int)ArrayHelper::getValue($row, 'Value', 0);
//                $this->setCount($count);
//                return $count;
//            }
//        }
        return 0;
    }

    /**
     * Метод ищет в sphinx по установленным фильтрам и возвращает массив id.
     * @return array
     */
    public function find()
    {

        $query = $this->getQuery();
        $this->setFilters();

//        $rows = $query
//            ->orderBy($this->order_by)
//            ->limit($this->limit)
//            ->all(\Yii::$app->get('sphinx'));

        //return $this->prepareSphinxFoundRows($rows);
        return [];
    }

    /**
     * Метод запускает find и отдаёт список id найденных записей.
     * @return array
     */
    public function findIdList()
    {
        $found = $this->find();
        return array_map('intval', ArrayHelper::getColumn($found, 'id'));
    }

    /**
     * Возвращает список найденных id.
     * @return array
     */
    public function getFoundIdList(): array
    {
        if (null === $this->found_id_list) {
            $this->found_id_list = $this->findIdList();
        }
        return $this->found_id_list;
    }

    /**
     * @return array
     * @var string $column
     */
    public function findColumnList($column)
    {
        $found = $this->find();
        return array_map('intval', ArrayHelper::getColumn($found, $column));
    }

    /**
     * Для дополнительной обработки записей после поиска в sphinx.
     * @param array $rows
     * @return array
     */
    protected function prepareSphinxFoundRows(array $rows)
    {
        $this->found_id_list = ArrayHelper::getColumn($rows, 'id', []);
        return $rows;
    }

    /**
     * Группировка.
     * @param array $columns
     * @return $this
     */
    public function setGroupBy(array $columns)
    {
        $this->group_by = $columns;
        return $this;
    }

    /**
     * Сортировка поиска Sphinx.
     * @param string $order_by
     * @return $this
     */
    public function setOrderBy($order_by)
    {
        $this->order_by = (string)$order_by;
        return $this;
    }

    /**
     * Устанавливает Limit поиска Sphinx.
     * @param integer $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = intval($limit);
        return $this;
    }

    /**
     * @param Pagination $pagination
     * @return $this;
     */
    public function setPagination(Pagination $pagination)
    {
        $this->pagination = $pagination;
        return $this;
    }

    /**
     * @return null|Pagination
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @param int $per_page_count
     * @return $this
     */
    public function setPerPageCount($per_page_count)
    {
        $this->per_page_count = (int)$per_page_count;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = (int)$count;
        return $this;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function setQueryString($query)
    {
        // todo HtmlPurifier
        $this->query_string = (string)strip_tags(trim($query));
        return $this;
    }

    /**
     * @param integer $part_size
     * @return $this
     */
    public function setChunkPartSize($part_size)
    {
        $this->chunk_part_size = (int)$part_size;
        return $this;
    }

    /**
     * not in id filter.
     * @param array $ids
     * @return $this
     */
    public function setFilterNotInId(array $ids)
    {
        $ids = array_map('intval', $ids);
        $this->filter_not_in_id = $ids;
        return $this;
    }

    /**
     * in id filter.
     * @param array $ids
     * @return $this
     */
    public function setFilterInId(array $ids)
    {
        $ids = array_map('intval', $ids);
        $this->filter_in_id = $ids;
        return $this;
    }
}