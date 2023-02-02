<?php

namespace common\libs\form\components;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use common\libs\form\traits\OptionsTrait;
use common\libs\form\traits\AttributesTrait;

/**
 * Class Select
 * @package common\libs\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class Select extends Control {
    use OptionsTrait;
    use AttributesTrait;

    protected function beforeRender() {
        parent::beforeRender();
        if ($this->isMultiple()) {
            $this->addAttribute('multiple', 'multiple');
        }
    }

    /**
     * @return string
     */
    public function render(): string {
        parent::render();
        $options = $this->getOptionsAsArray();
        $options_attributes = [];
        /** @var Option $option */
        foreach ($this->getOptions() as $option) {
            $options_attributes['options'][$option->getOptionName()] = $option->getAttributes();
        }
        return Html::dropDownList($this->getName(), $this->getValue(), $options, ArrayHelper::merge($this->getAttributes(), $options_attributes));
    }
}