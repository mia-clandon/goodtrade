<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Input as BaseInput;

/**
 * Class SearchBin
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class SearchBin extends BaseInput {

    const TEMPLATE_NAME = 'search_bin';

    protected function beforeRender() {
        parent::beforeRender();
        $this->setTemplateName(self::TEMPLATE_NAME);
        $this->addAttribute('maxlength', 12)
            ->addAttribute('autocomplete', 'off');
        $this->addClasses(['input-field', 'search-bin']);
    }

    public function render(): string {
        $control = parent::render();
        return parent::renderTemplate([
            'control' => $control,
            'is_hidden' => $this->is_hidden,
        ]);
    }
}