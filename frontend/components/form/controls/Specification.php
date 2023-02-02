<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Input;

/**
 * Контрол для работы характеристик товара.
 * Class Specification
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Specification extends Input {

    protected function beforeRender() {
        $this->setType(static::TYPE_HIDDEN)
            ->addClass('specification-data-input');
    }

    public function getValue() {
        $value = parent::getValue();
        if (!is_null($value)) {
            $value = json_decode(urldecode($value), true);
            return $value;
        }
        return [];
    }

    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control' => $control,
        ]);
    }
}