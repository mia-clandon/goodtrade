<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Checkbox as BaseCheckbox;

/**
 * Class Checkbox
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Checkbox extends BaseCheckbox {

    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control' => $control,
        ]);
    }
}