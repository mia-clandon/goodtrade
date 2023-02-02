<?php

namespace common\libs\form\components;

use yii\helpers\Html;

/**
 * TextArea
 * Class TextArea
 * @package common\libs\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class TextArea extends Control {

    /**
     * @return string
     */
    public function render(): string {
        parent::render();
        return Html::textarea($this->getName(), $this->getValue(), $this->getAttributes());
    }
}