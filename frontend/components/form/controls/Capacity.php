<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Control;
use common\libs\form\components\Input;
use yii\helpers\ArrayHelper;

/**
 * Class Capacity
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Capacity extends Control {

    const KEY_FROM = 'from';
    const KEY_TO = 'to';

    /** @var null|Input */
    private $from_input;
    /** @var null|Input */
    private $to_input;

    /**
     * @return $this
     */
    private function initFromControl() {
        $this->from_input = (new Input())->setName(self::KEY_FROM);
        $value = ArrayHelper::getValue($this->getValue(), self::KEY_FROM, '');
        $this->from_input->setValue($value);
        return $this;
    }

    /**
     * @return $this
     */
    private function initToControl() {
        $this->to_input = (new Input())->setName(self::KEY_TO);
        $value = ArrayHelper::getValue($this->getValue(), self::KEY_TO, '');
        $this->to_input->setValue($value);
        return $this;
    }

    public function getValue() {
        $value = parent::getValue();
        $from = (float)ArrayHelper::getValue($value, self::KEY_FROM, 0) ?? 0;
        $to = (float)ArrayHelper::getValue($value, self::KEY_TO, 0) ?? 0;
        return [self::KEY_FROM => $from, self::KEY_TO => $to];
    }

    protected function beforeRender() {
        parent::beforeRender();

        $this->initFromControl();
        $this->initToControl();

        /** @var Input $range_control */
        foreach ([$this->from_input, $this->to_input] as $range_control) {
            $range_control
                ->addClass('input-field')
                ->setType(Input::TYPE_NUMBER)
                ->addAttribute('autocomplete', 'off')
            ;
            $range_control->setName($this->getName().'['.$range_control->getName().']');
        }
    }

    public function render(): string {
        parent::render();
        return $this->renderTemplate([
            'from_control' => $this->from_input->render(),
            'to_control' => $this->to_input->render(),
        ]);
    }
}