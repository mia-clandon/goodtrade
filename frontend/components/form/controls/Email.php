<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Input;

/**
 * Class Email
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Email extends GroupControl {

    /**
     * @param string $value
     * @return Input
     */
    protected function getControl($value = '') {
        $control = (new Input())
            ->addClass('input-field')
            ->setValue($value)
            ->setName($this->getName())
            ->setIsMultiple()
            ->setPlaceholder($this->getPlaceholder())
            ->setTitle($this->getTitle())
        ;
        foreach ($this->getRules() as $rule) {
            // правила валидации.
            $control->addRule($rule);
        }
        return $control;
    }

    public function render(): string {
        parent::render();
        $controls = $this->getControls();
        return $this->renderTemplate([
            'controls' => $controls,
            'controls_count' => count($controls),
        ]);
    }
}