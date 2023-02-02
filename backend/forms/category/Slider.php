<?php

namespace backend\forms\category;

use backend\components\form\controls\Image;
use common\models\CategorySlider;
use common\models\firms\Firm;

use common\libs\form\components\Option;
use common\libs\form\validators\client\Required as RequiredClient;
use common\models\Category;

use backend\components\form\controls\Button;
use backend\components\form\controls\Input;
use backend\components\form\controls\Select;
use backend\components\form\controls\Selectize;
use backend\components\form\controls\TextArea;
use backend\components\form\Form;
use yii\helpers\Url;

/**
 * Class Update
 * @package backend\forms\category
 * @author yerganat
 */
class Slider extends Form {

    public function initControls(): void {
        parent::initControls();

        /** @var CategorySlider $tag_model */
        $tag_model = $this->getModel();

        $tag_id_control = (new Input())
            ->setType(Input::TYPE_HIDDEN)
            ->setValue(!$tag_model->isNewRecord ? $tag_model->id : 0)
            ->setName('category_slider_id');
        $this->registerControl($tag_id_control);

        $type_control = (new Selectize())
            ->setTitle('Тип записи')
            ->setPlaceholder('Выберите тип записи')
            ->setName('tag')
            ->setArrayOfOptions((new CategorySlider())->geTags())
        ;
        $this->registerControl($type_control);

        $this->registerControl($this->getCategoryControl());

        /** @var CategorySlider $model */
        $model = $this->getModel();
        $image_component = (new Image())
            ->setTitle('Фото слайда')
            ->setName('image')
            ->setRemoveAction(Url::to(['category-slider/remove-image']))
            ->setAdditionalParams(['data-entity-id' => $model->id])
        ;
        $this->registerControl($image_component);


        $title_input = (new Input())
            ->setName('title')
            ->setPlaceholder('Введите название записи')
            ->setJsValidator([(new RequiredClient())])
            ->addRule(['required'])
        ;
        $this->registerControl($title_input);

        $link_input = (new Input())
            ->setName('link')
            ->setPlaceholder('Источник/ссылка')
        ;
        $this->registerControl($link_input);

        $firm_control = (new Selectize())
            ->setName('firm_id')
            ->setTitle('Компания')
            ->setPlaceholder('Выберите компанию')
        ;
        $this->populateFirm($firm_control);
        $this->registerControl($firm_control);

        $text_component = (new TextArea())
            ->setLoadCKEditor()
            ->setPlaceholder('Описание товара')
            ->setName('description')
            ->addAttribute('rows', 3)
        ;
        $this->registerControl($text_component);

        $button = (new Button())
            ->setName('submit')
            ->setContent('Сохранить запись')
            ->addClass('save-product')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
        ;
        $this->registerControl($button);
    }

    /**
     * @return Selectize
     */
    public function getCategoryControl() {
        $category_control = (new Selectize())
            ->setName('category_id')
            ->setTitle('Выберите категорию')
            ->setPlaceholder('Выберите категорию')
        ;
        $this->populateCategories($category_control);
        return $category_control;
    }


    /**
     * @param Selectize $select
     * @return Selectize
     */
    private function populateCategories(Selectize $select) {
        /** @var CategorySlider $model */
        $model = $this->getModel();
        $options = (new Category())->getNestedOptions();

        // выбранные категории.
        $selected_categories = [];
        if ($model && !$model->isNewRecord) {
            $selected_categories[] = $model->category_id;
        }
        $select->setValue($selected_categories);
        $select->addOption((new Option('', 'Ничего не выбранно')));

        foreach ($options as $option_id => $option) {
            $option = new Option($option_id, $option);
            $select->addOption($option);
        }
        return $select;
    }


    /**
     * Пополнение компании.
     * @param Select $select
     * @return Select
     */
    private function populateFirm(Select $select) {
        /** @var Firm[] $firm_list */
        $firm_list = Firm::find()->where(['status' => 1])->all();

        $select->setValue([$this->getModel()->firm_id]);

        $option = new Option(0, 'Нет');
        $select->addOption($option);

        foreach ($firm_list as $firm) {
            $option = new Option($firm->id, $firm->title);
            $select->addOption($option);
        }
        return $select;
    }


    /**
     * @return array
     */
    public function validate(): array {
        /** @var CategorySlider $model */
        $model = $this->getModel();
        $this->populateModel();
        $model->validate();
        $this->populateErrorsFromAR($model->getErrors());
        return parent::validate();
    }

    protected function populateModel(): void {

        /** @var CategorySlider $model */
        $model = $this->getModel();
        $form_data = $this->getFormData();

        foreach ($form_data as $attribute_name => $value) {
            switch ($attribute_name) {
                case "image": {
                    if (is_array($value)) {
                        $model->setImageForUpload($value);
                    }
                    continue;
                } break;
                default: {
                    if ($model->hasAttribute($attribute_name)) {
                        $model->setAttribute($attribute_name, $value);
                    }
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function save(): bool  {
        /** @var CategorySlider $model */
        $model = $this->getModel();
        $saved = $model->save();
        if ($saved === false) {
            // beforeSave может сгенерировать ошибку сохранения.
            $this->populateErrorsFromAR($model->getErrors());
        }
        return $saved;
    }
}