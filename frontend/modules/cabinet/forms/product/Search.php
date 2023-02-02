<?php

namespace frontend\modules\cabinet\forms\product;

use common\libs\form\Form;
use common\libs\form\components\Input;

use frontend\components\form\controls\Button;

/**
 * Class Search
 * @package frontend\modules\cabinet\forms\product
 * @author Артём Широких kowapssupport@gmail.com
 */
class Search extends Form {

    /** @var null|Input */
    private $query_control;

    protected function initControls(): void {
        parent::initControls();

        $query = $this->getQueryControl();
        $this->registerControl($query);

        $submit = (new Button())
            ->setName('submit')
            ->setContent('Найти')
            ->setClasses(['btn','btn-blue','btn-outline'])
            ->setType(Button::TYPE_SUBMIT)
        ;
        $this->registerControl($submit);
    }

    /**
     * @return Input
     */
    public function getQueryControl(): Input {
        if (null === $this->query_control) {
            $this->query_control = (new Input())
                ->setName('query')
                ->setType(Input::TYPE_TEXT)
                ->addAttribute('required', 'required')
                ->setPlaceholder('Введите название вашего товара')
            ;
        }
        return $this->query_control;
    }
}