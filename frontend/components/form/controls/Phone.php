<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Input as BaseInput;

/**
 * Class Phone
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Phone extends GroupControl {

    /**
     * @param string $value
     * @return BaseInput
     */
    protected function getControl($value = '') {
        $control = (new BaseInput())
            ->setValue($value)
            ->setName($this->getName())
            ->setIsMultiple()
            ->setPlaceholder($this->getPlaceholder())
            ->setType(Input::TYPE_PHONE)
            ->setTitle($this->getTitle())
            ->addAttribute('autocomplete', 'off')
            ->setPlaceholder($this->getPlaceholder())
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