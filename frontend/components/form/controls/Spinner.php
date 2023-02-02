<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Input;

/**
 * Class Spinner
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Spinner extends Input {

    const DEFAULT_VALUE = 0;

    public function beforeRender() {
        parent::beforeRender();

        if (!$this->value) {
            $this->value = self::DEFAULT_VALUE;
        }

        $this->addClasses(['spinner-input', 'input-field'])
            ->setType(Input::TYPE_NUMBER);
    }

    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control' => $control,
        ]);
    }
}