<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Input as BaseInput;

/**
 * Class Input
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Input extends BaseInput {

    const PHONE_TYPE = 'input-tel';
    const EMAIL_TYPE = 'input-mail';
    const BIN_TYPE = 'input-bin';
    const GEO_TYPE = 'input-geo';

    /** @var string Тип инпута */
    private $type = '';

    /** @var string Подсказка рядом с label */
    private $label_tip = '';

    /** @var null|integer */
    private $max_length;
    /** @var null|integer */
    private $min_length;

    /**
     * @param string $type
     * @return $this
     */
    public function setInputType(string $type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @param int $length
     * @return $this
     */
    public function setMaxLength(int $length) {
        $this->max_length = $length;
        $this->addAttribute('maxlength', $length);
        return $this;
    }

    /**
     * @param int $length
     * @return $this
     */
    public function setMinLength(int $length) {
        $this->min_length = $length;
        $this->addAttribute('minlength', $length);
        return $this;
    }

    /**
     * @param string $tip
     * @return $this
     */
    public function setLabelTip(string $tip) {
        $this->label_tip = $tip;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabelTip(): string {
        return $this->label_tip;
    }

    protected function beforeRender() {
        parent::beforeRender();
        $this->addClass('input-field');
    }

    public function render(): string {
        $control = parent::render();
        return parent::renderTemplate([
            'control' => $control,
            'input_type' => $this->type,
            'label_tip' => $this->label_tip,
        ]);
    }
}