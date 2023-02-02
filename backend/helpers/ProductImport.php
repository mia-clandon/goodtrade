<?php

namespace backend\helpers;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class ProductImport
 * @package backend\heplers
 * @author Артём Широких kowapssupport@gmail.com
 */
class ProductImport {

    const TYPE_KEY = 'type';
    const TYPE_HEADER = 'header';

    private $file_path = null;

    /** @var null|integer - номер начала заголовка таблицы. */
    private $header_row_index = null;
    /** @var null|integer - количество столбцов в строке. */
    private $cols_in_row_count = null;
    /** @var null|integer - порядковый номер колонки с названием товара. */
    private $title_col_index = null;
    /** @var null|integer - порядковый номер колонки с ценой товара.*/
    private $price_col_index = null;

    /** @var null|array - данные с файла excel. */
    private $data = null;

    /**
     * Возвращает данные в виде массива.
     * @return array|null
     * @throws Exception
     */
    public function getAsArray() {
        if (is_null($this->data)) {
            throw new Exception('Не обходимо запустить Parse(), данные пустые.');
        }
        return $this->data;
    }

    /**
     * Возвращает данные в виде JSON строки.
     * @return string
     */
    public function getAsJson() {
        $data = $this->getAsArray();
        return Json::encode($data);
    }

    /**
     * Метод собирает данные с excel файла, и сохраняет их в обьект.
     * @return $this
     * @throws Exception
     */
    public function parse() {

        if (is_null($this->file_path) || !file_exists($this->file_path)) {
            throw new Exception('Необходимо установить путь к прайс листу.');
        }

        if (is_null($this->cols_in_row_count)) {
            throw new Exception('Необходимо установить количество столбцов в строке.');
        }

        if (is_null($this->header_row_index)) {
            throw new Exception('Необходимо установить номер начала заголовка таблицы.');
        }

        if (is_null($this->title_col_index)) {
            throw new Exception('Необходимо установить порядковый номер колонки с названием товара.');
        }

        if (is_null($this->price_col_index)) {
            throw new Exception('Необходимо установить порядковый номер колонки с ценой товара.');
        }

        $file_name = (string)$this->file_path;

        $reader = \PHPExcel_IOFactory::createReaderForFile($file_name);

        $excel_object = $reader->load($file_name);
        $worksheet = $excel_object->getActiveSheet();

        $highest_row = $worksheet->getHighestRow();


        /*
         * Не всегда правильно определяет - т.к. в нашем случае могут быть колонки вне таблицы.
        $highest_column = $worksheet->getHighestColumn();
        $highest_column_index = \PHPExcel_Cell::columnIndexFromString($highest_column);
        */

        $highest_column_index = $this->cols_in_row_count;

        // определение объединенных ячеек (со значениями и без) и заполнение ячеек без значение (merged).
        $this->fillMergedCells($worksheet);

        // заполнение массива значениями из ячеек.
        $rows = [];
        for ($row = $this->header_row_index; $row <= $highest_row; ++$row) {
            for ($col = 0; $col <= $highest_column_index; ++$col) {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $col_key = $col;
                if ($col_key == ($this->title_col_index-1)) {
                    $col_key = 'title';
                }
                if ($col_key == ($this->price_col_index-1)) {
                    $col_key = 'price';
                }
                $rows[$row][$col_key] = $cell->getValue();
            }
        }

        $this->prepareRows($rows);
        $this->data = $rows;
        unset($rows);
        return $this;
    }

    /**
     * Метод фильтрует строки.
     * Пробегает по всем строкам подчищая:
     * 1) Пустые строки.
     * 2) Убирает пустые колонки в строке.
     * @param array $rows
     */
    protected function prepareRows(&$rows) {
        foreach ($rows as $key => $row) {
            if ($this->allIsEmpty($row)) {
                unset($rows[$key]);
                continue;
            }
            if ($this->isCategoryRow($row)) {
                $rows[$key][self::TYPE_KEY] = 'category';
            }
            else if ($this->isHeadRow($key)) {
                $rows[$key][self::TYPE_KEY] = self::TYPE_HEADER;
            }
            else {
                $rows[$key][self::TYPE_KEY] = 'product';
            }
        }
    }

    /**
     * Метод определяет, строка таблицы является ли заголовком.
     * @param integer $key
     * @return boolean
     */
    protected function isHeadRow($key) {
        if ($this->header_row_index == $key) {
            return true;
        }
        return false;
    }

    /**
     * Метод определяет, строка таблицы является ли категорией.
     * @param array $row
     * @return boolean
     */
    protected function isCategoryRow(array $row) {
        if (!isset($row[0])) {
            return false;
        }
        $first_element = $row[0];
        $last_element = isset($row[count($row)-1]) ? $row[count($row)-1] : NULL;
        if ($first_element == $last_element) {
            return true;
        }
        unset($row[0]);
        if ($this->allIsEmpty($row)) {
            return true;
        }
        return false;
    }

    /**
     * @param array $array
     * @return bool
     */
    private function allIsEmpty(array $array) {
        foreach ($array as $value) {
            if ($value) {
                return false;
            }
        }
        return true;
    }

    /**
     * Определение объединенных ячеек (со значениями и без) и заполнение ячеек без значение (merged).
     * @param \PHPExcel_Worksheet $worksheet
     */
    protected function fillMergedCells(&$worksheet) {
        // определение объединенных ячеек (со значениями и без).
        $merge_cells = $worksheet->getMergeCells();

        foreach ($merge_cells as $key => $merge_cell) {
            $cell_coordinate = explode(':', $merge_cell);

            $from = ArrayHelper::getValue($cell_coordinate, 0);
            $to = ArrayHelper::getValue($cell_coordinate, 1);
            $from_cell_letter = (string)substr($from, 0, 1);
            $from_cell_number = intval(preg_replace('/[^0-9]+/', '', $from), 10);
            $to_cell_letter = (string)substr($to, 0, 1);
            $to_cell_number = intval(preg_replace('/[^0-9]+/', '', $to), 10);

            $new_coordinates = '';
            if ($from_cell_letter === $to_cell_letter) {
                for($i = $from_cell_number; $i<=$to_cell_number; $i++) {
                    $new_coordinates .= $from_cell_letter.(string)$i.':';
                }
                $new_coordinates = rtrim($new_coordinates, ':');
                if (!empty($new_coordinates)) {
                    $merge_cells[$key] = $new_coordinates;
                }
            }
        }

        $null_val_coordinates = [];
        $not_null_val_coordinates = [];

        foreach ($merge_cells as $coordinate => $merge_cell) {
            $merge_cells_exploded = explode(':', $merge_cell);
            foreach ($merge_cells_exploded as $cell_coordinate) {
                $cell_value = $worksheet->getCell($cell_coordinate)
                    ->getValue();
                if (is_null($cell_value)) {
                    $null_val_coordinates[$coordinate][] = $cell_coordinate;
                }
                else {
                    $not_null_val_coordinates[$coordinate] = $cell_value;
                }
            }
        }

        // заполнение пустых ячеек.
        foreach ($null_val_coordinates as $coordinate => $null_coordinates) {
            if (array_key_exists($coordinate, $not_null_val_coordinates)) {
                $value = $not_null_val_coordinates[$coordinate];
                foreach ($null_coordinates as $null_coordinate) {
                    $worksheet->setCellValue($null_coordinate, $value);
                }
            }
        }
    }

    /**
     * Метод пробегается по данным и возвращает строку с заголовком таблицы.
     * @return array
     */
    public function getHeader() {
        foreach ($this->data as $item) {
            $type = ArrayHelper::getValue($item, self::TYPE_KEY);
            if ($type == self::TYPE_HEADER) {
                return $item;
            }
        }
        return [];
    }

    /**
     * Метод возвращает тело документа.
     * @return array
     */
    public function getBody() {
        $body_rows = [];
        foreach ($this->data as $item) {
            $type = ArrayHelper::getValue($item, self::TYPE_KEY);
            if ($type == self::TYPE_HEADER) {
                continue;
            }
            $body_rows[] = $item;
        }
        return $body_rows;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setFilePath($path) {
        $this->file_path = (string)$path;
        return $this;
    }

    /**
     * @param int|null $cols_in_row_count
     * @return $this
     */
    public function setColsInRowCount($cols_in_row_count) {
        if ($cols_in_row_count <= 0) {
            return $this;
        }
        $this->cols_in_row_count = (int)$cols_in_row_count-1;
        return $this;
    }

    /**
     * @param int|null $header_row_index
     * @return $this
     */
    public function setHeaderRowIndex($header_row_index) {
        $this->header_row_index = (int)$header_row_index;
        return $this;
    }

    /**
     * @param int|null $title_col_index
     * @return $this
     */
    public function setTitleColIndex($title_col_index) {
        $this->title_col_index = (int)$title_col_index;
        return $this;
    }

    /**
     * @param int|null $price_col_index
     * @return $this
     */
    public function setPriceColIndex($price_col_index) {
        $this->price_col_index = (int)$price_col_index;
        return $this;
    }
}