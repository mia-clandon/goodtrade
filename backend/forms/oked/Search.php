<?php

namespace backend\forms\oked;

use yii\helpers\Url;

use backend\components\form\controls\Bootstrap;
use backend\components\form\controls\Icon;
use backend\components\form\Form;
use backend\components\form\controls\Input;
use backend\components\form\controls\Button;

/**
 * Class Search
 * Форма поиска ОКЭД.
 * @package backend\forms
 * @author Артём Широких kowapssupport@gmail.com
 */
class Search extends Form {

    protected function initControls(): void {
        parent::initControls();

        $from_input = (new Input())
            ->setTitle('Код ОКЭД от')
            ->setName('from')
            ->setPlaceholder('Код ОКЭД от')
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $from_input->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($from_input);

        $from_input = (new Input())
            ->setTitle('Код ОКЭД до')
            ->setName('to')
            ->setPlaceholder('Код ОКЭД до')
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $from_input->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($from_input);

        $button = (new Button())
            ->setName('submit')
            ->setContent('Найти')
            ->setClass('btn btn-primary ml-2')
            ->setType(Button::TYPE_SUBMIT);
        $this->registerControl($button);

        $reset = (new Button())
            ->setName('reset')
            ->setContent('Сбросить фильтр')
            ->setClass('btn btn-warning ml-2')
            ->setType(Button::TYPE_RESET)
            ->setRedirectOnClick(Url::to(['oked/index']));
        $this->registerControl($reset);

        $update_sphinx = (new Button())
            ->setName('update_sphinx')
            ->setContent('Переиндексировать')
            ->addClass('btn btn-danger ml-2')
            ->setIcon(Icon::GLYPHICON_FIRE)
            ->setType(Button::TYPE_BUTTON)
            ->setRedirectOnClick(Url::to(['oked/update-index']));
        $this->registerControl($update_sphinx);
    }
}