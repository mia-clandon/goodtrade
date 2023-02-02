<?php

namespace backend\forms\location;

use yii\helpers\Url;

use common\libs\form\components\Option;
use common\models\Location;

use backend\components\form\controls\Bootstrap;
use backend\components\form\controls\Icon;
use backend\components\form\controls\Select;
use backend\components\form\Form;
use backend\components\form\controls\Input;
use backend\components\form\controls\Button;

/**
 * Class Search
 * Форма поиска в списке организаций.
 * @package backend\forms
 * @author Артём Широких kowapssupport@gmail.com
 */
class Search extends Form {

    protected function initControls(): void {
        parent::initControls();

        $title_input = (new Input())
            ->setTitle('Название города')
            ->setName('title')
            ->setPlaceholder('Название города')
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $title_input->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($title_input);

        $region_select = (new Select())
            ->setTitle('Область')
            ->setName('region')
            ->setPlaceholder('Выберите область')
            ->addOption((new Option())->add(0, ''))
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $location_model = (new Location());
        $regions = $location_model->getPossibleRegions();
        foreach ($regions as $name => $id) {
            $region_select->addOption((new Option($id, $name)));
        }
        $region_select->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($region_select);

        # кнопки действий.
        $button = (new Button())->setName('submit')->setContent('Поиск по локациям')->setClass('btn btn-primary')->setType(Button::TYPE_SUBMIT);
        $this->registerControl($button);

        $reset = (new Button())->setName('reset')->setContent('Сбросить фильтр')->setClass('btn btn-warning')->setType(Button::TYPE_RESET)
            ->setRedirectOnClick(Url::to(['location/index']))
        ;
        $this->registerControl($reset);

        $update_index = (new Button())
            ->setId('update_index')
            ->setName('update_index')
            ->addClass('btn-danger')
            ->setContent('Запустить переиндексацию Sphinx')
            ->setIcon(Icon::GLYPHICON_FIRE)
            ->setType(Button::TYPE_BUTTON)
            ->setRedirectOnClick(Url::to(['location/update-index']))
        ;
        // TODO-F: необходимо ограничить доступ к этому компоненту.
        $this->registerControl($update_index);
    }
}