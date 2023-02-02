<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Option;
use common\models\goods\Product;
use common\libs\form\components\Control;
use common\libs\form\components\Input as BaseInput;
use common\libs\form\components\Select as BaseSelect;
use yii\helpers\ArrayHelper;

/**
 * Class Price
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Price extends Control {

    /** Name, для цены. */
    const PRICE_NAME = 'price';
    /** Name, для единиц измерения. */
    const UNIT_NAME = 'unit';

    private $price_control = null;
    private $unit_control = null;

    /**
     * @return array
     */
    private function getAllUnits() {
        return (new Product())->getAllUnits();
    }

    public function setValue($value) {

        $selected_price = ArrayHelper::getValue($value, self::PRICE_NAME, '');
        $selected_unit = ArrayHelper::getValue($value, self::UNIT_NAME, '');

        $this->getPriceControl()->setValue($selected_price);
        $this->getUnitsControl()->setValue($selected_unit);
    }

    /**
     * Возвращает значение контрола в необходимом формате.
     * @return array
     */
    public function getValue() {
        return [
            self::PRICE_NAME => (float)$this->getPriceControl()->getValue(),
            self::UNIT_NAME => (int)$this->getUnitsControl()->getValue(),
        ];
    }

    /**
     * @return BaseInput
     */
    protected function getPriceControl() {
        if (is_null($this->price_control)) {
            $this->price_control = (new BaseInput())
                ->setName($this->getName().'['.self::PRICE_NAME.']')
                ->setType(Input::TYPE_NUMBER)
                ->addClass('input-field')
            ;
        }
        return $this->price_control;
    }

    /**
     * @return BaseSelect
     */
    protected function getUnitsControl() {
        if (is_null($this->unit_control)) {
            $unit_items = $this->getAllUnits();
            $units = (new BaseSelect())
                ->setName($this->getName().'['.self::UNIT_NAME.']')
            ;
            foreach ($unit_items as $unit_id => $unit_name) {
                $option = new Option($unit_id, $unit_name);
                $units->addOption($option);
            }
            $this->unit_control = $units;
        }
        return $this->unit_control;
    }

    public function render(): string {
        parent::render();
        return $this->renderTemplate([
            'units_control' => $this->getUnitsControl()->render(),
            'price_control' => $this->getPriceControl()->render(),
        ]);
    }
}