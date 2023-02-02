<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Input as BaseInput;
use common\libs\parser\vk\Base;

/**
 * Class SearchProduct
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class SearchProduct extends BaseInput {

    const TEMPLATE_NAME = 'search_product';

    protected function beforeRender() {
        parent::beforeRender();
        $this->setTemplateName(self::TEMPLATE_NAME);
        $this->addClass('input-field');
    }

    /**
     * @return BaseInput
     */
    private function getIdControl() {
        $control = (new BaseInput())
            ->setName('product_id')
            ->setType(BaseInput::TYPE_HIDDEN);
        return $control;
    }

    public function render(): string {
        $control = parent::render();
        return parent::renderTemplate([
            'control' => $control,
            'id_control' => $this->getIdControl()->render(),
        ]);
    }
}