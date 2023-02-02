<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Control;

/**
 * Class GroupControl
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class GroupControl extends Control {

    /** @var array */
    private $controls_data = [];

    /** @var Input[] */
    private $controls = [];

    public function setValue($value) {
        if (is_array($value)) {
            $this->controls_data = $value;
        }
        return parent::setValue($value);
    }

    /**
     * @param string $value
     * @return Control|null
     */
    protected function getControl($value = '') {
        return null;
    }

    /**
     * @return Control[]
     */
    protected function getControls() {
        if (!empty($this->controls)) {
            return $this->controls;
        }
        if (!empty($this->controls_data)) {
            foreach ($this->controls_data as $value) {
                $this->controls[] = $this->getControl($value);
            }
        }
        else {
            $this->controls[] = $this->getControl();
        }
        return $this->controls;
    }

    public function validate() {
        $errors = [];
        foreach ($this->getControls() as $control) {
            $errors = array_merge($control->validate(), $errors);
        }
        return $errors;
    }

    public function isValid() {
        $is_valid = true;
        foreach ($this->getControls() as $control) {
            if (!$control->isValid()) {
                return false;
            }
        }
        return $is_valid;
    }
}