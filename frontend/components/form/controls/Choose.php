<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Input;
use common\libs\form\traits\OptionsTrait;

/**
 * Class Choose
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Choose extends Input {
    use OptionsTrait;

    public function beforeRender() {
        parent::beforeRender();
        $this->setType(self::TYPE_HIDDEN);
    }

    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control' => $control,
            'options' => $this->getOptionsAsArray(),
        ]);
    }
}