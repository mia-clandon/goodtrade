<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Select as SelectBase;

/**
 * Class Select
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Select extends SelectBase {

    /*
    public function getValue() {
        $value = parent::getValue();
        if (!$value) {
            $this->setValue($this->getFirstOptionValue());
        }
        return $value;
    }
    */

    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control' => $control,
        ]);
    }
}