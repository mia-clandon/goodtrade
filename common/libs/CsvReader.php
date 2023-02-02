<?php

namespace common\libs;

use yii\db\QueryBuilder;

/**
 * Class CsvReader
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class CsvReader {

    /** @var  string Путь к файлу */
    private $file;

    /** @var int Необходим для построения запроса в базу, количество записей вставляемых одним запросом. */
    private $limit = 10;

    /** @var array ['Название колонки из шапки файла' => 'Название колонки в базе'] */
    private $header_map = [];

    /** @var array */
    private $rows = [];

    /** @var int  */
    private $count_insert_queries = 0;

    /** @var int  */
    private $count_insert_rows = 0;

    /** @var string  */
    private $table_name = '';

    /** Категория логгирования */
    const LOGGER_CATEGORY = 'csv_log';

    /**
     * @param $header
     * @return bool
     */
    private function compareHeaders($header) {
        return (empty(array_diff(array_keys($this->header_map), $header)));
    }

    /**
     * @return int
     * @throws \yii\db\Exception
     */
    private function buildQuery() {
        if (empty($this->rows)) {
            return false;
        }
        try {
            $build = new QueryBuilder($this->getConnection());
            $columns = array_values($this->header_map);
            $query = $build->batchInsert($this->getTableName(), $columns, $this->rows);
            $this->count_insert_queries++;
            // execute
            $inserted_rows = $this->getConnection()->createCommand($query)->execute();
            $this->count_insert_rows += $inserted_rows;
            return $inserted_rows;
        }
        catch (\Exception $e) {
            \Yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * @param array $row
     * @return $this
     */
    private function appendRow(array $row) {
        $this->rows[] = $row;
        return $this;
    }

    /**
     * @return $this
     */
    private function clearRows() {
        $this->rows = [];
        return $this;
    }

    /**
     * @return array ['count_insert_queries' => 'Количество insert запросов',
     * 'count_insert_rows' => 'Количество вставленных записей']
     * @throws \Exception
     */
    public function save() {

        if (empty($this->table_name)) {
            throw new \Exception('Укажите имя таблицы.');
        }

        if (file_exists($this->file)) {

            if (($handle = fopen($this->file, 'r')) !== false) {

                // Строка с заголовками файла.
                $header = fgetcsv($handle, null, ';');
                if (!$this->compareHeaders($header)) {
                    throw new \Exception('Шапка файла не совпадает с маппингом.');
                }

                $iteration = 0;

                // Чтение оставшихся строк.
                while (($data = fgetcsv($handle, null, ';')) !== false) {

                    // если в data попала шапка файла - игнор
                    if (empty(array_diff($data, array_keys($this->header_map)))) {
                        continue;
                    }

                    if ($iteration <= $this->limit) {
                        $this->appendRow($data);
                    }
                    else {
                        $this->appendRow($data);
                        $this->buildQuery();
                        $this->clearRows();
                        $iteration = 0;
                    }
                    unset($data);
                    $iteration++;
                }

                // оставшиеся записи
                if (count($this->rows) > 0) {
                    $this->buildQuery();
                    $this->clearRows();
                }

                fclose($handle);
                return [
                    'count_insert_queries' => $this->count_insert_queries,
                    'count_insert_rows' => $this->count_insert_rows,
                ];
            }
            else {
                throw new \Exception('Ошибка при чтении файла.');
            }
        }
        else {
            throw new \Exception('File not found - '. $this->file.'.');
        }
    }

    /**
     * @return $this
     */
    public function clear() {
        $this->count_insert_queries = 0;
        $this->count_insert_rows = 0;
        $this->clearRows();
        return $this;
    }

    /**
     * @return string
     */
    public function getTableName() {
        return $this->table_name;
    }

    /**
     * @param string $table_name
     * @return $this
     */
    public function setTableName($table_name) {
        $this->table_name = $table_name;
        return $this;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param array $map
     * @return $this
     */
    public function setHeaderMap(array $map) {
        $this->header_map = $map;
        return $this;
    }

    /**
     * @param $file
     * @return $this
     */
    public function setFilePath($file) {
        $this->file = $file;
        return $this;
    }

    /**
     * @return \yii\db\Connection
     */
    private function getConnection() {
        return \Yii::$app->getDb();
    }
}