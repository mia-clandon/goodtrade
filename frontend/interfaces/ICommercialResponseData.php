<?php

namespace frontend\interfaces;

/**
 * Interface ICommercialResponseData
 * Интерфейс для определения структуры data-класса формы коммерческого предложения.
 * @package frontend\interfaces
 * @author Артём Широких kowapssupport@gmail.com
 */
interface ICommercialResponseData {

    /**
     * Идентификатор города.
     * @param integer $city_id
     * @return static
     */
    public function setCompanyCity($city_id);

    /**
     * Получение идентификатора города.
     * @return integer
     */
    public function getCompanyCity();

    /**
     * Установка адреса организации.
     * @param string $address
     * @return static
     */
    public function setCompanyAddress($address);

    /**
     * Получение адреса организации.
     * @return string
     */
    public function getCompanyAddress();

    /**
     * Установка массива email организации.
     * @param array $emails
     * @return static
     */
    public function setCompanyEmails(array $emails);

    /**
     * Получение массива email организации.
     * @return array
     */
    public function getCompanyEmails();

    /**
     * Установка массива телефонов организации.
     * @param array $phones
     * @return static
     */
    public function setCompanyPhones(array $phones);

    /**
     * Получение массива телефонов организации.
     * @return array
     */
    public function getCompanyPhones();

    /**
     * Установка массива условий доставки.
     * @param array $delivery_terms
     * @return static
     */
    public function setDeliveryTerms(array $delivery_terms);

    /**
     * Получение массива условий доставки.
     * @return array
     */
    public function getDeliveryTerms();

    /**
     * @param string $bin
     * @return static
     */
    public function setCompanyBin($bin);

    public function getCompanyBin();

    /**
     * @param string $bank
     * @return static
     */
    public function setCompanyBank($bank);

    public function getCompanyBank();

    /**
     * @param string $bik
     * @return static
     */
    public function setCompanyBik($bik);

    public function getCompanyBik();

    /**
     * @param string $iik
     * @return static
     */
    public function setCompanyIik($iik);

    public function getCompanyIik();

    /**
     * @param string $kbe
     * @return static
     */
    public function setCompanyKbe($kbe);

    public function getCompanyKbe();

    /**
     * @param string $knp
     * @return static
     */
    public function setCompanyKnp($knp);

    public function getCompanyKnp();

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