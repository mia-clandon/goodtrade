<?php

namespace frontend\interfaces;

/**
 * Interface ICommercialRequestData
 * Интерфейс для определения структуры data-класса формы коммерческого запроса.
 * @package frontend\interfaces
 * @author Артём Широких kowapssupport@gmail.com
 */
interface ICommercialRequestData {

    /**
     * На все модификации.
     * @param boolean $flag
     * @return static
     */
    public function setIsAllModifications($flag);

    /**
     * На все модификации ?
     * @return boolean
     */
    public function isAllModifications();

    /**
     * Установка значений характеристик.
     * @param array $terms
     * @return static
     */
    public function setTerms(array $terms);

    /**
     * Id значений характеристик на который был послан коммерческий запрос.
     * @return array
     */
    public function getTerms();

    /**
     * Возвращает JSON со всеми данными для хранения в базе данных.
     * @return string
     */
    public function getData();

    /**
     * @param string $json
     * @return static
     */
    public function setData($json);

}