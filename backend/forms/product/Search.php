<?php

namespace backend\forms\product;

use yii\helpers\Url;

use common\models\goods\Product;

use backend\components\form\controls\Bootstrap;
use backend\assets\FancyBoxAsset;
use backend\assets\PartIndexerAsset;
use backend\components\form\controls\Button;
use backend\components\form\controls\Icon;
use backend\components\form\controls\Input;
use backend\components\form\controls\Select;
use backend\components\form\Form;

/**
 * Class Search
 * @package backend\forms\product
 * @author Артём Широких kowapssupport@gmail.com
 */
class Search extends Form {

    protected function registerFormAssets() {
        PartIndexerAsset::register(\Yii::$app->getView());
        // plug-in fancy box
        FancyBoxAsset::register(\Yii::$app->getView());
    }

    public function initControls(): void {
        parent::initControls();
        $this->registerJsScript();

        $title_input = (new Input())
            ->setTitle('Название')
            ->setName('title')
            ->setPlaceholder('Название товара')
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $title_input->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($title_input);

        $category_control = (new Update())
            ->getCategoryControl()
            ->setTitle('Выберите категорию')
            ->setName('categories')
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $category_control->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($category_control);

        $firm_control = (new Update())
            ->getFirmControl()
            ->setTitle('Организация')
            ->setName('firm_id')
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $firm_control->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($firm_control);

        $status_control = (new Select())
            ->setTitle('Выберите статус')
            ->setName('status')
            ->setArrayOfOptions((new Product())->getStatuses())
            ->setControlColWidth(Bootstrap::COL_SM_8)
        ;
        $status_control->getLabelControl()
            ->setControlColWidth(Bootstrap::COL_SM_4);
        $this->registerControl($status_control);

        $button = (new Button())->setName('submit')->setContent('Поиск по товарам')->setClass('btn btn-primary')->setType(Button::TYPE_SUBMIT);
        $this->registerControl($button);

        $reset = (new Button())->setName('reset')->setContent('Сбросить фильтр')->setClass('btn btn-warning')->setType(Button::TYPE_RESET)
            ->setRedirectOnClick(Url::to(['product/index']))
        ;
        $this->registerControl($reset);

        $update_index = (new Button())
            ->setId('update-index')
            ->setName('update_index')
            ->addClass('btn-danger')
            ->setContent('Запустить переиндексацию Sphinx')
            ->setIcon(Icon::GLYPHICON_FIRE)
            ->setType(Button::TYPE_BUTTON)
        ;
        $this->registerControl($update_index);
    }
}