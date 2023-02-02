<?php

namespace backend\forms\test;

use backend\components\form\controls\Button;
use backend\components\form\controls\Range;
use backend\components\form\Form;

/**
 * Class Test
 * @package backend\forms\test
 * @author Артём Широких kowapssupport@gmail.com
 */
class Test extends Form {

    public function initControls(): void {
        parent::initControls();

        $range = new Range();
        $range->setName('range');
        $range->setFrom(1);
        $range->setTo(100);
        //$range->setValue([Range::RANGE_FROM_PROPERTY => 12, Range::RANGE_TO_PROPERTY => 13]);
        $range->setValue(14);

        $this->registerControl($range);

        $button_control = (new Button())
            ->setName('submit')
            ->setContent('Сохранить')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
        ;
        $this->registerControl($button_control);
    }
}