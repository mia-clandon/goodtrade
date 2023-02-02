<?php

namespace backend\forms\category;

use yii\helpers\Url;

use backend\components\form\controls\TextArea;
use backend\components\form\controls\Button;
use backend\components\form\controls\Image;
use backend\components\form\controls\Input;
use backend\components\form\controls\Selectize;
use backend\components\form\Form;

use common\libs\form\components\Control;
use common\libs\form\components\Option;
use common\models\Category;
use common\libs\form\validators\client\Required as RequiredClient;
use common\models\CategoryImage;

/**
 * Class Update
 * Форма обновления/создания категорий.
 * @package backend\forms
 * @author Артём Широких kowapssupport@gmail.com
 */
class Update extends Form {

    /** @var null|integer */
    private $parent_id;

    protected function initControls(): void {

        /** @var Category $model */
        $model = $this->getModel();

        // название категории.
        $title_control = (new Input())
            ->setName('title')
            ->setJsValidator([new RequiredClient()])
        ;
        $this->registerControl($title_control);

        if(!$model->getChild()->count()) {
            // ключевые слова
            $meta_keywords = (new Input())
                ->setName('meta_keywords');
            $this->registerControl($meta_keywords);

            // ключевое описание.
            $meta_description = (new TextArea())
                ->setName('meta_description')
                ->setRowsCount(5);
            $this->registerControl($meta_description);
        }

        // фотографии категорий.
        $image_control = (new Image())
            ->setTitle('Загрузите фото')
            ->setName('images')
            ->setIsMultiple()
            //TODO:
            //->setMaxFileCount();
            ->setRemoveAction(Url::to(['category/remove-image']))
            ->setSizes(200, 100, 'AUTO')
            ->setEnableSorting()
        ;
        $this->populateImages($image_control);
        $this->registerControl($image_control);

        // краткое описание.
        $small_text_control = (new TextArea())
            ->setName('small_text')
            ->setRowsCount(5)
        ;
        $this->registerControl($small_text_control);

        // полное описание.
        $text_control = (new TextArea())
            ->setName('text')
            ->setLoadCKEditor();
        $this->registerControl($text_control);

        // родительская категория.
        $parent_control = $this->getParentCategoryControl();
        if (is_numeric($this->parent_id) && $this->getModel()->isNewRecord) {
            $parent_control->setValue($this->parent_id);
        }
        $this->registerControl($parent_control);

        // класс иконки.
        $icon_class_control = (new Selectize())
            ->setName('icon_class')
            ->setTitle('Класс иконки (css) - используется в контроле выбора сферы деятельности.')
        ;
        $icon_class_control = $this->populateIcons($icon_class_control);
        $this->registerControl($icon_class_control);

        // кнопки действий.
        $submit_control = (new Button())
            ->setName('submit')
            ->setContent('Сохранить')
            ->setClass('btn btn-primary')
            ->setType(Button::TYPE_SUBMIT)
        ;
        $this->registerControl($submit_control);

        if (!$this->getModel()->isNewRecord) {
            $add_control = (new Button())
                ->setName('add_more')
                ->setContent('Добавить еще.')
                ->setType(Button::TYPE_BUTTON)
                ->setButtonType(Button::BTN_TYPE_LINK)
                ->setRedirectOnClick(Url::to(['category/update']))
            ;
            $this->registerControl($add_control);
        }
    }

    /**
     * @param int $parent_id
     * @return $this
     */
    public function setParentId($parent_id) {
        $this->parent_id = (int)$parent_id;
        return $this;
    }

    /**
     * Заполняет модель данными из формы.
     */
    protected function populateModel(): void {

        /** @var Category $model */
        $model = $this->getModel();
        $form_data = $this->getFormData();

        foreach ($form_data as $attribute_name => $value) {
            if ($attribute_name === 'images') {
                $model->setImages(is_array($value) ? $value : []);
            }
            else if ($attribute_name == 'images_order') {
                $model->setImagesOrder(!is_array($value) ? [$value] : $value);
            }
            if ($model->hasAttribute($attribute_name)) {
                $model->setAttribute($attribute_name, $value);
            }
        }
    }

    /**
     * Валидация формы + валидация модели.
     * @return array
     */
    public function validate(): array {
        $this->populateModel();
        $this->getModel()->validate();
        $this->populateErrorsFromAR($this->getModel()->getErrors());
        return parent::validate();
    }

    public function save(): bool {
        return $this->getModel()->save();
    }

    /**
     * @param Selectize $selectize
     * @return Selectize
     */
    private function populateIcons(Selectize $selectize) {
        /** @var Category $model */
        $model = $this->getModel();
        $selectize->addOption(new Option('', ''));
        foreach ($model->getPossibleIconClasses() as $class => $name) {
            $selectize->addOption(new Option($class, $name));
        }
        return $selectize;
    }

    /**
     * @return Control
     */
    private function getParentCategoryControl() {
        $parent_component = (new Selectize())
            ->setName('parent')
        ;
        /** @var Category $model */
        $model = $this->getModel();
        if ($model) {
            $options = [0 => 'Корневая категория.'] + $model->getNestedOptions();
            foreach ($options as $value => $name) {
                // отключаю опцию равную текущей записи.
                $option = new Option($value, $name);
                if ($model->id == $value && $value !== 0) {
                    $option->addAttribute('disabled', 'disabled');
                }
                $parent_component->addOption($option);
            }
        }
        return $parent_component;
    }

    /**
     * @param Image $image_control
     * @return Image
     */
    private function populateImages(Image $image_control) {
        /** @var Category $model */
        $model = $this->getModel();
        if (!$model->isNewRecord) {
            /** @var CategoryImage[] $images */
            $images = $model->getImages()->select(['id', 'image'])->orderBy('position')->all();
            $result = [];
            foreach ($images as $img) {
                $result[$img->id] = $img->image;
            }
            $image_control->setValue($result);
        }
        return $image_control;
    }
}