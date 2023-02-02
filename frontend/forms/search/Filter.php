<?php

namespace frontend\forms\search;

use common\libs\form\Form;
use common\models\goods\helpers\DeliveryTerms;

use frontend\components\form\controls\b2b\Button;
use frontend\components\form\controls\b2b\CapacityRange;
use frontend\components\form\controls\b2b\Delivery;
use frontend\components\form\controls\b2b\PriceRange;

/**
 * Class Filter
 * @package frontend\forms\site
 * @author Артём Широких kowapssupport@gmail.com
 */
class Filter extends Form {

    public function initControls(): void {
        parent::initControls();
        $this->addClass('filter-form row');

        //Диапазон цен.
        $price_range = (new PriceRange())
            ->setName('price')
            ->setTitle('Цена');
        $this->registerControl($price_range);

        //Условия поставки.
        $delivery_terms = (new Delivery())
            ->setName('terms')
            ->setTitle('Условия поставки');
        $this->registerControl($delivery_terms);

        //Объем производства.
        $capacities = (new CapacityRange())
            ->setName('capacities')
            ->setTitle('Объем производства');
        $this->registerControl($capacities);

        //Submit.
        $submit_control = (new Button())
            ->setName('submit')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_PRIMARY)
            ->setFullButton()
            ->setTitle('Применить фильтр')
        ;
        $this->registerControl($submit_control);

        $this->addTemplateVars([
            'delivery_terms' => (new DeliveryTerms())->getAllDeliveryTerms(),
        ]);
    }
}