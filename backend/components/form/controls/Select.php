<?php

namespace backend\components\form\controls;

use backend\components\form\traits\SizeBootstrapTrait;
use common\libs\form\components\Select as BaseSelect;

/**
 * Class Select
 * @package backend\components\bootstrap\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class Select extends BaseSelect {
    use SizeBootstrapTrait;

    protected $template_name = 'select';

    /** @var null|Label */
    private $label_control;

    protected function beforeRender() {
        parent::beforeRender();
        $this->addClass('form-control');
    }

    /**
     * @return Label|null
     */
    public function getLabelControl() {
        if (empty($this->getTitle())) {
            return null;
        }
        if (null === $this->label_control) {
            $this->label_control = (new Label())
                ->setContent($this->getTitle());
        }
        return $this->label_control;
    }

    public function render(): string {
        $control = parent::render();
        $label = $this->getLabelControl();
        return $this->renderTemplate([
            'control' => $control,
            'control_col_width' => $this->getControlColWidth(),
            'label' => $label ? $label->render() : '',
            'errors' => $this->getErrors(),
            'errors_string' => $this->getErrorsAsString(','),
        ]);
    }
}