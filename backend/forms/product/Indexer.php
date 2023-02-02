<?php

namespace backend\forms\product;

use backend\components\form\Form;
use backend\components\form\controls\Input;
use backend\components\form\controls\Button;

use common\libs\traits\RegisterJsScript;
use common\libs\form\validators\client\Number as NumberClient;
use common\libs\form\validators\client\Required as RequiredClient;

/**
 * Class Indexer
 * Форма расчета партии для индексации.
 * @package backend\forms
 * @author Артём Широких kowapssupport@gmail.com
 */
class Indexer extends Form {

    use RegisterJsScript;

    protected function initControls(): void {
        parent::initControls();
        $count_input = (new Input())
            ->setTitle('Количество обрабатываемых записей')
            ->setName('part_count')
            ->setPlaceholder('Введите число')
            ->setJsValidator([new RequiredClient(), new NumberClient()])
            ->setValue("5000")
            ->addAttribute('disabled', 'disabled')
        ;
        $this->registerControl($count_input);

        $button = (new Button())
            ->setId('calculate-indexer-parts')
            ->setName('submit')
            ->setContent('Расчитать')
            ->setType(Button::TYPE_BUTTON)
        ;
        $this->registerControl($button);

        $button = (new Button())
            ->setId('clear-index')
            ->setName('clear_index')
            ->setContent('Очистить индекс')
            ->setType(Button::TYPE_BUTTON)
            ->addClass('btn-danger')
        ;
        $this->registerControl($button);
    }
}