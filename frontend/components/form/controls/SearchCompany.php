<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Input as BaseInput;

/**
 * Class SearchCompany
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class SearchCompany extends BaseInput {

    const TEMPLATE_NAME = 'search_company';

    protected function beforeRender() {
        parent::beforeRender();
        $this->setTemplateName(self::TEMPLATE_NAME);
        $this->addClasses(['input-field', 'search-company']);
    }

    public function render(): string {
        $control = parent::render();
        return parent::renderTemplate([
            'control' => $control,
        ]);
    }
}