<?php

namespace backend\forms\profile;

use yii\helpers\Url;
use common\libs\traits\RegisterJsScript;
use backend\assets\FancyBoxAsset;
use backend\assets\PartIndexerAsset;
use backend\components\form\controls\Bootstrap;
use backend\components\form\controls\Icon;
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
    use RegisterJsScript;

    protected function registerFormAssets() {
        PartIndexerAsset::register(\Yii::$app->getView());
        // plug-in fancy box
        FancyBoxAsset::register(\Yii::$app->getView());
    }

    protected function initControls(): void {
        parent::initControls();
        $this->registerJsScript();

        $title_input = (new Input())
            ->setTitle('Название')
            ->setName('title')
            ->setPlaceholder('Название организации')
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $title_input->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($title_input);

        $activity_input = (new Input())
            ->setTitle('Вид деятельности')
            ->setName('activity')
            ->setPlaceholder('Поиск по виду деятельности')
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $activity_input->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($activity_input);

        $leader_input = (new Input())
            ->setTitle('Руководитель')
            ->setPlaceholder('Поиск по руководителю')
            ->setName('leader')
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $leader_input->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($leader_input);

        $address_input = (new Input())
            ->setTitle('Юридический адрес')
            ->setPlaceholder('Поиск по юридическому адресу')
            ->setName('legal_address')
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $address_input->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($address_input);

        $button = (new Button())
            ->setName('submit')
            ->setContent('Поиск по организациям')
            ->setClass('btn btn-primary')
            ->setType(Button::TYPE_SUBMIT)
        ;
        $this->registerControl($button);

        $reset = (new Button())
            ->setName('reset')
            ->setContent('Сбросить фильтр')
            ->setClass('btn btn-warning')
            ->setType(Button::TYPE_RESET)
            ->setRedirectOnClick(Url::to(['profile/index']))
        ;
        $this->registerControl($reset);

        $update_index = (new Button())
            ->setId('update-index')
            ->setName('update_index')
            ->setContent('Запустить переиндексацию Sphinx')
            ->addClass('btn-danger')
            ->setIcon(Icon::GLYPHICON_FIRE)
            ->setType(Button::TYPE_BUTTON)
        ;
        // TODO-F: необходимо ограничить доступ к этому компоненту.
        $this->registerControl($update_index);
    }
}