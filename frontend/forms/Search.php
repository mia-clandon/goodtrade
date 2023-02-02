<?php

namespace frontend\forms;

use common\libs\form\components\Button;
use common\libs\form\components\Input;
use common\libs\form\Form;

/**
 * Class Search
 * @package frontend\forms
 * @property string $query
 * @author Артём Широких kowapssupport@gmail.com
 */
class Search extends Form {

    const ACTIVE_TUMBLER_FIRMS = 'firms';
    const ACTIVE_TUMBLER_PRODUCTS = 'products';

    private $product_control;
    private $location_control;
    private $submit_control;

    private $product_count = 0;
    private $firms_count = 0;

    private $product_find_url = '#';
    private $firm_find_url = '#';

    private $active_tumbler = self::ACTIVE_TUMBLER_PRODUCTS;

    public function initControls(): void {

        $this->addClass('bar-top__search-form');

        // инпут для поиска
        $this->registerControl($this->getProductQueryControl());
        // выбор региона
        //todo: временно скрыли.
        //$this->registerControl($this->getLocationControl());
        // submit
        $this->registerControl($this->getSubmitControl());

        $this->addTemplateVars([
            'product_count'     => $this->getProductCount(),
            'firms_count'       => $this->getFirmsCount(),
            'active_tumbler'    => $this->active_tumbler,
            'firm_find_url'     => $this->firm_find_url,
            'product_find_url'  => $this->product_find_url,
        ]);
    }

    /**
     * Получение контрола с инпутом для поиска.
     * @return Input
     */
    public function getProductQueryControl() {
        if (is_null($this->product_control)) {
            $this->product_control = (new Input())
                ->setName('query')
                ->setType(Input::TYPE_TEXT)
                ->setPlaceholder('Поиск...')
                ->addAttribute('required', 'required')
//                ->setJsValidator([
//                    (new Required())
//                ])
            ;
        }
        return $this->product_control;
    }

    /**
     * Получение контрола с инпутом выбора региона.
     * @return Input
     */
    public function getLocationControl() {
        if (is_null($this->location_control)) {
            $this->location_control = (new Input())
                ->setName('location')
                ->setType(Input::TYPE_TEXT)
                ->setPlaceholder('Регион')
                ->addClass('input-field')
            ;
        }
        return $this->location_control;
    }

    /**
     * Контрол Submit
     * @return Button
     */
    public function getSubmitControl() {
        if (is_null($this->submit_control)) {
            $this->submit_control = (new Button())
                ->setName('submit')
                ->setContent('Найти')
                ->addClass('btn btn-blue')
                ->setType(Button::TYPE_SUBMIT)
            ;
        }
        return $this->submit_control;
    }

    /**
     * @return int
     */
    public function getProductCount() {
        return $this->product_count;
    }

    /**
     * @param int $product_count
     * @return $this
     */
    public function setProductCount($product_count) {
        $this->product_count = (int)$product_count;
        return $this;
    }

    /**
     * @return int
     */
    public function getFirmsCount() {
        return $this->firms_count;
    }

    /**
     * @param int $firms_count
     * @return $this
     */
    public function setFirmsCount($firms_count) {
        $this->firms_count = (int)$firms_count;
        return $this;
    }

    /**
     * Устанавливает активный тумблер в поиске (товары или организации).
     * @param string $tumbler
     * @return $this
     */
    public function setActiveTumbler($tumbler) {
        if (in_array($tumbler, [self::ACTIVE_TUMBLER_PRODUCTS, self::ACTIVE_TUMBLER_FIRMS])) {
            $this->active_tumbler = $tumbler;
        }
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setProductFindUrl($url) {
        $this->product_find_url = (string)urldecode($url);
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setFirmFindUrl($url) {
        $this->firm_find_url = (string)urldecode($url);
        return $this;
    }
}