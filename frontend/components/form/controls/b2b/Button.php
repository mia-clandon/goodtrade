<?php

namespace frontend\components\form\controls\b2b;

use common\libs\form\components\Button as BaseButton;

/**
 * Class Button
 * @package frontend\components\form\controls\b2b
 * @author yerganat
 */
class Button extends BaseButton {

    const BTN = 'button';

    const BTN_FULL = 'button_full';

    const BTN_PRIMARY = 'button_primary';
    const BTN_DANGER = 'button_danger';
    const BTN_SUCCESS = 'button_success';

    /**
     * @param string $type
     * @return $this
     */
    public function setButtonType(string $type) {
        if (in_array($type, [self::BTN_PRIMARY, self::BTN_DANGER, self::BTN_SUCCESS])) {
            $this->addClass($type);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function setFullButton() {
        $this->addClass(self::BTN_FULL);
        return $this;
    }

    protected function beforeRender() {
        parent::beforeRender();
        $this->setTemplateName('b2b/button');
    }

    public function render(): string {
        $this->addClass(self::BTN);
        $this->setContent('<span class="button__text">'.$this->getTitle().'</span>');
        return $this->renderTemplate([
            'control' => parent::render(),
        ]);
    }
}