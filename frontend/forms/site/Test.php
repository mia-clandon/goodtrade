<?php

namespace frontend\forms\site;

use common\libs\form\Form;
use frontend\components\form\controls\b2b\Delivery;
use frontend\components\form\controls\b2b\PriceRange;
use frontend\components\form\controls\Button;

/**
 * Class Test
 * @package frontend\forms\site
 * @author Артём Широких kowapssupport@gmail.com
 */
class Test extends Form {

    protected function initControls(): void {

        $price_range = new PriceRange();
        $price_range->setName('price');
        $this->registerControl($price_range);


        $delivery = new Delivery();
        $delivery->setName('terms');
        $delivery->setTitle('Условия поставки');
        $delivery->setValue([5]);
        $this->registerControl($delivery);

        $submit_control = (new Button())
            ->setName('submit')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonColor(Button::BTN_COLOR)
            ->addClasses(['btn', 'btn-block'])
            ->setContent('отправить')
        ;
        $this->registerControl($submit_control);

    }
}