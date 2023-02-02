<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Input;

/**
 * Class StandardFile
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class StandardFile extends Input {

    /** @var string Подсказка рядом с label */
    private $label_tip = '';

    protected $template_name = 'standard_file';

    public function beforeRender() {
        parent::beforeRender();
        $this->setType(Input::TYPE_FILE);
    }

    /**
     * @param string $tip
     * @return $this
     */
    public function setLabelTip(string $tip) {
        $this->label_tip = $tip;
        return $this;
    }

    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control'       => $control,
            'label_tip'     => $this->label_tip,
            'input_type'    => Input::TYPE_FILE,
            'errors'        => $this->getErrors(),
            'errors_string' => $this->getErrorsAsString(','),
        ]);
    }
}