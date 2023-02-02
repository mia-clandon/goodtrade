<?php

namespace common\libs\form\components;

use yii\helpers\Html;

/**
 * Class Checkbox
 * @package common\libs\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class Checkbox extends Control {

    /**
     * @param bool $flag
     * @return $this
     */
    public function setChecked($flag = true) {
        if ((bool)$flag) {
            $this->addAttribute('checked', 'checked');
            $this->setValue(1);
        }
        else {
            $this->removeAttribute('checked');
            $this->setValue(0);
        }
        return $this;
    }

    private function getHiddenControl() {
        return Html::input('hidden', $this->getName(), 0, [
            'class' => 'hidden-checkbox',
        ]);
    }

    /**
     * @return string
     */
    public function render(): string {
        parent::render();
        $checked = ($this->getValue()) ? true : false;
        return implode('', [
            $this->getHiddenControl(),
            Html::checkbox($this->getName(), $checked, $this->getAttributes()),
        ]);
    }
}