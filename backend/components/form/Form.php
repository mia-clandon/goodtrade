<?php

namespace backend\components\form;

use backend\components\form\controls\Copy;
use backend\components\form\controls\Selectize;
use common\libs\form\components\Control;
use common\libs\form\Form as BaseForm;

/**
 * Class Form
 * @package backend\components\form
 * @author Артём Широких kowapssupport@gmail.com
 */
class Form extends BaseForm
{

    public const FORM_HORIZONTAL_MODE = 'form-horizontal';
    public const FORM_INLINE_MODE = 'form-inline';

    /** @var array  */
    private $form_modes = [
        self::FORM_HORIZONTAL_MODE,
        self::FORM_INLINE_MODE,
    ];

    /** @var null|string */
    private $form_mode;

    /**
     * @param $mode
     * @return $this
     */
    public function setFormMode(string $mode): self {
        if (\in_array($mode, $this->form_modes, true)) {
            $this->form_mode = $mode;
        }
        return $this;
    }

    protected function initControls(): void {
        parent::initControls();
        if (null !== $this->form_mode) {
            $this->addClass($this->form_mode);
        }
    }

    /**
     * Загружает ресурсы каждого контрола. (для ajax форм).
     * в тестовом режиме.
     * @return $this
     */
    public function loadResources(): static
    {
        $controls = $this->getControls();
        $need_connect_js_validator = false;
        foreach ($controls as $control) {
            if ($control->hasClientValidator() && $need_connect_js_validator === false) {
                $need_connect_js_validator = true;
            }
            if ($control instanceof Selectize) {
                /** @var Selectize $control */
                $control->registerScripts();
            }
            if ($control instanceof Copy) {
                $control->registerScripts();
            }
        }
        if ($need_connect_js_validator) {
            $this->connectValidatorJs();
        }
        return $this;
    }
}