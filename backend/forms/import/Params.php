<?php

namespace backend\forms\import;

use backend\components\form\controls\Button;
use backend\components\form\controls\Input;
use backend\components\form\Form;

use common\libs\form\validators\client\Number;
use common\libs\form\validators\client\Required;
use yii\helpers\Url;

/**
 * Class Params
 * @package backend\forms\import
 * @author Артём Широких kowapssupport@gmail.com
 */
class Params extends Form {

    /** @var int  */
    private $price_id = 0;
    /** @var int  */
    private $firm_id = 0;

    protected function initControls(): void {
        $this->registerJsScript();

        $col_count_control = (new Input())
            ->setTitle('Количество колонок в строке')
            ->setType(Input::TYPE_NUMBER)
            ->setPlaceholder('7')
            //TODO: временно
            ->setValue(7)
            ->setJsValidator([(new Required()), (new Number())])
            ->setName('col_count_in_row');
        $this->registerControl($col_count_control);

        $header_col_index_control = (new Input())
            ->setTitle('Номер строки заголовка таблицы')
            ->setType(Input::TYPE_NUMBER)
            ->setPlaceholder('11')
            //TODO: временно
            ->setValue(11)
            ->setJsValidator([(new Required()), (new Number())])
            ->setName('header_col_index');
        $this->registerControl($header_col_index_control);

        $title_col_index_control = (new Input())
            ->setTitle('Номер колонки с названием товара')
            ->setType(Input::TYPE_NUMBER)
            ->setPlaceholder('2')
            //TODO: временно
            ->setValue(2)
            ->setJsValidator([(new Required()), (new Number())])
            ->setName('title_col_index');
        $this->registerControl($title_col_index_control);

        $price_col_index_control = (new Input())
            ->setTitle('Номер колонки с ценой товара')
            ->setType(Input::TYPE_NUMBER)
            ->setPlaceholder('6')
            //TODO: временно
            ->setValue(6)
            ->setJsValidator([(new Required()), (new Number())])
            ->setName('price_col_index');
        $this->registerControl($price_col_index_control);

        $hidden_id_control = (new Input())
            ->setType(Input::TYPE_HIDDEN)
            ->setName('id')
            ->setValue($this->getPriceId());
        ;
        $this->registerControl($hidden_id_control);

        $firm_id_control = (new Input())
            ->setType(Input::TYPE_HIDDEN)
            ->setName('firm_id')
            ->setValue($this->getFirmId());
        ;
        $this->registerControl($firm_id_control);

        $button = (new Button())
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
            ->setName('submit')
            ->setContent('Показать таблицу')
            ->setType(Button::TYPE_SUBMIT)
            ->addDataAttribute('url', Url::to(['import/get-excel-table']))
        ;
        $this->registerControl($button);
    }

    /**
     * @return int
     */
    public function getFirmId() {
        return $this->firm_id;
    }

    /**
     * @param int $firm_id
     * @return  $this
     */
    public function setFirmId($firm_id) {
        $this->firm_id = (int)$firm_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriceId() {
        return $this->price_id;
    }

    /**
     * @param int $price_id
     * @return  $this
     */
    public function setPriceId($price_id) {
        $this->price_id = (int)$price_id;
        return $this;
    }
}