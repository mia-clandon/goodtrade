<?php

namespace backend\components\form\controls;

/**
 * Class Input
 * @package backend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Price extends Input  {

    protected $template_name = 'input';

    public function render(): string {

        $this->addAddOn('тг', self::ADD_ON_POSITION_RIGHT);
        $this->setType(self::TYPE_NUMBER);

        return parent::render();
    }
}