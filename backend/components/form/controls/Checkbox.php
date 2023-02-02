<?php

namespace backend\components\form\controls;

use common\libs\form\components\Checkbox as BaseCheckBox;

/**
 * Class Checkbox
 * @package backend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Checkbox extends BaseCheckBox {

    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control' => $control,
        ]);
    }
}