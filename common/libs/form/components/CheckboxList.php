<?php

namespace common\libs\form\components;

use common\libs\form\traits\OptionsTrait;
use yii\helpers\Html;

/**
 * Class CheckboxList
 * @package common\libs\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class CheckboxList extends Control {

    use OptionsTrait;

    /**
     * @return $this
     */
    public function setMultiple() {
        $this->addAttribute('multiple', 'multiple');
        return $this;
    }

    /**
     * @return string
     */
    public function render(): string {
        parent::render();
        return Html::checkboxList($this->getName(), $this->getValue(), $this->getOptionsAsArray(), $this->getAttributes());
    }
}