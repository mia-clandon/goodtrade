<?php

namespace frontend\components\form\controls;

use common\libs\form\components\TextArea as BaseTextArea;

/**
 * Class TextArea
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class TextArea extends BaseTextArea {

    protected function beforeRender() {
        parent::beforeRender();
        $this->setTemplateName('textarea');
        $this->addClass('input-field');
        $this->addClass('input-field-textarea');
    }

    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control' => $control,
        ]);
    }
}