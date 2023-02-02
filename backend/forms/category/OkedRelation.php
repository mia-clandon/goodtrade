<?php

namespace backend\forms\category;

use backend\components\form\controls\Button;
use backend\components\form\controls\Input;
use backend\components\form\Form;

use common\models\Category;
use common\models\Oked;

use yii\base\Exception;

/**
 * Class OkedRelation
 * @package backend\forms\category
 * @author Артём Широких kowapssupport@gmail.com
 */
class OkedRelation extends Form {

    /** @var Category|null */
    private $category_model;

    public function initControls(): void {
        parent::initControls();

        $this->setId('oked-relation-form');
        $this->setTemplateFileName('oked_relation');
        $this->addTemplateVars([
            'oked_list' => $this->getCategoryOkedList(),
        ]);

        $category_id_control = (new Input())
            ->setName('category_id')
            ->setType(Input::TYPE_HIDDEN)
            ->setValue($this->getCategoryModel()->id);
        $this->registerControl($category_id_control);

        $search_title_control = (new Input())
            ->setName('name')
            ->setPlaceholder('Название ОКЭД');
        $this->registerControl($search_title_control);

        $search_from_control = (new Input())
            ->setName('from_oked')
            ->setValue($this->getMinOked())
            ->setPlaceholder('ОКЭД от ...');
        $this->registerControl($search_from_control);

        $search_to_control = (new Input())
            ->setName('to_oked')
            ->setValue($this->getMaxOked())
            ->setPlaceholder('... ОКЭД до');
        $this->registerControl($search_to_control);

        $find_oked_control = (new Button())
            ->setType(Button::TYPE_BUTTON)
            ->setContent('Найти ОКЕД')
            ->setButtonType(Button::BTN_TYPE_WARNING)
            ->setName('find_oked');
        $this->registerControl($find_oked_control);
    }

    /**
     * @return Category
     * @throws Exception
     */
    public function getCategoryModel() {
        if (null === $this->category_model) {
            throw new Exception('Модель Category не установлена.');
        }
        return $this->category_model;
    }

    /**
     * @param Category $category_model
     * @return $this
     */
    public function setCategoryModel(Category $category_model) {
        $this->category_model = $category_model;
        return $this;
    }

    /**
     * @return array
     */
    private function getCategoryOkedList() {
        return $this->getCategoryModel()->getOked()->select(['key', 'name'])->all();
    }

    /**
     * @return int
     */
    private function getMinOked() {
        return (new Oked())->getMin();
    }

    /**
     * @return int
     */
    private function getMaxOked() {
        return (new Oked())->getMax();
    }
}