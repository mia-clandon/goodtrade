<?php

namespace backend\components\form\controls;

use backend\components\form\traits\SizeBootstrapTrait;
use common\libs\form\components\Label as BaseLabel;

/**
 * Class Label
 * @package backend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Label extends BaseLabel {
    use SizeBootstrapTrait;

    protected function beforeRender() {
        $this->addClass('control-label');

        $control_width = $this->getControlColWidth();
        if ($control_width !== null) {
            $this->addClass($control_width);
        }
    }
}