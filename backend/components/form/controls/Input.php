<?php

namespace backend\components\form\controls;

use common\libs\form\components\Input as BaseInput;

use backend\components\form\traits\SizeBootstrapTrait;

use yii\helpers\Html;

/**
 * Class Input
 * @package backend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Input extends BaseInput {
    use SizeBootstrapTrait;

    /** позиция аддона (перед input) */
    const ADD_ON_POSITION_LEFT = 1;
    /** позиция аддона (после input) */
    const ADD_ON_POSITION_RIGHT = 2;

    /** @var array - Дополнительные аддоны к интупу. */
    private $add_ons = [];

    /** @var null|Label */
    private $label_control;

    /**
     * @param string $content
     * @param int $position
     * @return $this
     */
    public function addAddOn($content, $position) {
        if (!empty($content) && in_array($position, [
            self::ADD_ON_POSITION_LEFT, self::ADD_ON_POSITION_RIGHT
        ])) {
            $this->add_ons[$position] = Html::tag('div', $content, [
                'class' => 'input-group-addon',
            ]);
        }
        return $this;
    }

    /**
     * @return Label|null
     */
    public function getLabelControl() {
        if ($this->isHiddenInput()) {
            return null;
        }
        if (empty($this->getTitle())) {
            return null;
        }
        if (null === $this->label_control) {
            $this->label_control = (new Label())
                ->setContent($this->getTitle());
        }
        return $this->label_control;
    }

    protected function beforeRender() {
        if ($this->getType() !== self::TYPE_HIDDEN) {
            $this->addClass('form-control');
        }
    }

    public function render(): string {
        $label = $this->getLabelControl();
        $control = parent::render();
        if ($this->getType() === self::TYPE_HIDDEN) {
            return parent::render();
        }
        return $this->renderTemplate([
            'control' => $control,
            'label' => $label !== null ? $label->render() : '',
            'control_col_width' => $this->getControlColWidth(),
            'errors' => $this->getErrors(),
            'errors_string' => $this->getErrorsAsString(','),
        ]);
    }
}