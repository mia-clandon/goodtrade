<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Button as BaseButton;

/**
 * Class Button
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Button extends BaseButton {

    const BTN_COLOR = 'btn-blue';

    /**
     * @param $color
     * @return $this
     */
    public function setButtonColor($color) {
        if (in_array($color, [self::BTN_COLOR])) {
            $this->addClass($color);
        }
        return $this;
    }

    public function render(): string {
        return parent::render();
    }
}