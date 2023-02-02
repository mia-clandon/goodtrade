<?php

namespace backend\forms\product;

use yii\helpers\Url;

use common\libs\form\components\Option;
use common\models\firms\Firm;
use common\models\goods\Images;
use common\models\goods\Product;
use common\models\Category;

use backend\components\form\controls\Button;
use backend\components\form\controls\Image;
use backend\components\form\controls\Input;
use backend\components\form\controls\Price;
use backend\components\form\controls\Select;
use backend\components\form\controls\Selectize;
use backend\components\form\controls\TextArea;
use backend\components\form\controls\Checkbox;
use backend\components\form\Form;
use backend\components\form\controls\SearchFirm;

/**
 * Class Update
 * @package backend\forms\product
 * @author Артём Широких kowapssupport@gmail.com
 */
class Update extends Form {

    public function initControls(): void {
        parent::initControls();

        $this->registerJsScript([
            'depends' => 'backend\assets\AppAsset',
        ]);
        /** @var Product $product_model */
        $product_model = $this->getModel();

        $product_id_control = (new Input())
            ->setType(Input::TYPE_HIDDEN)
            ->setValue(!$product_model->isNewRecord ? $product_model->id : 0)
            ->setName('product_id');
        $this->registerControl($product_id_control);

        $title_input = (new Input())
            ->setName('title')
            ->setPlaceholder('Сгенерированное название товара')
            ->setDisabled()
            ->setValue($product_model->isNewRecord ? '' : $product_model->getTitle())
        ;
        $this->registerControl($title_input);

        $this->registerControl($this->getFirmControl());
        $this->registerControl($this->getCategoryControl());

        $status_control = (new Selectize())
            ->setTitle('Статус товара')
            ->setPlaceholder('Выберите статус товара')
            ->setName('status')
            ->setArrayOfOptions((new Product())->getStatuses())
        ;
        $this->registerControl($status_control);

        $mark_control = (new Select())
            ->setTitle('Выделение товара')
            ->setPlaceholder('Выберите выделение товара')
            ->setName('mark_type')
            ->setArrayOfOptions(Product::getMarks())
        ;
        $this->registerControl($mark_control);

        $image_control = (new Image())
            ->setTitle('Выберите фото товара')
            ->setName('images')
            ->setIsMultiple()
            ->setRemoveAction(Url::to(['product/remove-image']))
            ->setSizes(200, 100, 'AUTO')
            ->setEnableSorting()
        ;
        $this->populateImages($image_control);
        $this->registerControl($image_control);

        $capacities_from = (new Input())
            ->addAddOn('от', Input::ADD_ON_POSITION_LEFT)
            ->setName('capacities_from')
            ->setType(Input::TYPE_NUMBER)
        ;
        $this->registerControl($capacities_from);

        $capacities_from = (new Input())
            ->addAddOn('до', Input::ADD_ON_POSITION_LEFT)
            ->setName('capacities_to')
            ->setType(Input::TYPE_NUMBER)
        ;
        $this->registerControl($capacities_from);

        $price_component = (new Price())
            ->setName('price')
            ->setPlaceholder('Цена товара за единицу измерения в тг');

        $this->registerControl($price_component);


        $unit_component = (new Selectize())
            ->setPlaceholder('Единица измерения')
            ->setName('unit')
        ;
        $this->populateUnits($unit_component);
        $this->registerControl($unit_component);

        $price_with_vat_component = (new Checkbox())
            ->setName('price_with_vat')
            ->setTitle('Цена с НДС ?')
        ;
        $this->registerControl($price_with_vat_component);

        $from_import_control = (new Checkbox())
            ->setDisabled()
            ->setName('from_import')
        ;
        $this->registerControl($from_import_control);

        $delivery_terms = (new Selectize())
            ->setIsMultiple()
            ->setPlaceholder('Выберите условия доставки')
            ->setName('delivery_terms')
        ;
        $this->populateDeliveryTerms($delivery_terms);
        $this->registerControl($delivery_terms);

        $text_component = (new TextArea())
            ->setLoadCKEditor()
            ->setPlaceholder('Описание товара')
            ->setName('text')
            ->addAttribute('rows', 10)
        ;
        $this->registerControl($text_component);

        $button = (new Button())
            ->setName('submit')
            ->setContent('Сохранить товар')
            ->addClass('save-product')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
        ;
        $this->registerControl($button);
    }

    /**
     * @return SearchFirm
     */
    public function getFirmControl() {
        $firm_control = (new SearchFirm())
            ->setName('firm_id')
            ->setPlaceholder('Введите БИН организации')
        ;
        $this->populateFirm($firm_control);
        return $firm_control;
    }

    /**
     * @return Selectize
     */
    public function getCategoryControl() {
        $category_control = (new Selectize())
            ->setName('category_id')
            ->setTitle('Выберите конечную категорию товара')
            ->setPlaceholder('Выберите конечную категорию товара')
        ;
        $this->populateCategories($category_control);
        return $category_control;
    }

    /**
     * @param Selectize $select
     * @return Selectize
     */
    private function populateUnits(Selectize $select) {
        /** @var Product $model */
        $model = $this->getModel();
        $units = [0 => 'не выбрано'] + $model->getAllUnits();
        foreach ($units as $unit_id => $unit) {
            $option = new Option($unit_id, $unit);
            $select->addOption($option);
        }
        return $select;
    }

    /**
     * @param Selectize $select
     * @return Selectize
     */
    private function populateDeliveryTerms(Selectize $select) {
        /** @var Product $model */
        $model = $this->getModel();
        $delivery_terms = $model->getDeliveryTermsHelper()->getAllDeliveryTerms();

        $selected_terms = [];
        if (!$model->isNewRecord) {
            $selected_terms = $model->getDeliveryTermsHelper()->getDeliveryTermsIds();
        }
        $select->setValue($selected_terms);

        foreach ($delivery_terms as $term_id => $term) {
            $option = new Option($term_id, $term);
            $select->addOption($option);
        }
        return $select;
    }

    /**
     * @param Selectize $select
     * @return Selectize
     */
    public function populateCategories(Selectize $select) {
        /** @var Product $model */
        $model = $this->getModel();
        $options = (new Category())->getNestedOptions();

        // выбранные категории.
        $selected_categories = [];
        if ($model && !$model->isNewRecord) {
            $selected_categories = $model->getCategoryIds();
        }
        $select->setValue($selected_categories);
        $select->addOption((new Option('', 'Ничего не выбранно')));

        // TODO: сделать $select->setDisabledOptions(array...)
        // TODO: так же сделать возможность вытянуть все 1м запросом. (has_child); 197 строка.

        foreach ($options as $option_id => $option) {
            $option = new Option($option_id, $option);
            /** @var Category $category */
            $category = Category::findOne($option_id);
            // Если сфера деятельности - не даю выбрать, и только конечная категория.
            $is_main_category = ($category && $category->parent == 0) ?  true : false;
            if ($is_main_category || ($category && $category->hasChild())) {
                $option->addAttribute('disabled', 'disabled');
            }
            $select->addOption($option);
        }
        return $select;
    }

    /**
     * @param Select $select_control
     * @return Select
     */
    private function populateFirm(Select $select_control) {
        /** @var Product $model */
        $model = $this->getModel();
        if ($model && !$model->isNewRecord) {
            $firm_id = $model->firm_id;
            if ($firm_id) {
                /** @var Firm $firm */
                $firm = Firm::findOne($firm_id);
                if ($firm) {
                    $select_control->addOption(new Option($firm->id, $firm->title));
                }
            }
        }
        return $select_control;
    }

    /**
     * @param Image $image_control
     * @return Image
     */
    private function populateImages(Image $image_control) {
        /** @var Product $model */
        $model = $this->getModel();
        if (!$model->isNewRecord) {
            /** @var Images[] $images */
            $images = $model->getImages()->select(['id', 'image'])->orderBy('position')->all();
            $result = [];
            foreach ($images as $img) {
                $result[$img->id] = $img->image;
            }
            $image_control->setValue($result);
        }
        return $image_control;
    }

    /**
     * @return array
     */
    public function validate(): array {
        /** @var Product $model */
        $model = $this->getModel();
        $this->populateModel();
        $model->validate();
        $this->populateErrorsFromAR($model->getErrors());
        return parent::validate();
    }

    protected function populateModel(): void {

        /** @var Product $model */
        $model = $this->getModel();
        $form_data = $this->getRequestData();

        foreach ($form_data as $attribute_name => $value) {
            switch ($attribute_name) {

                case "category_id": {
                    $model->setCategories([$value]);
                } break;

                case "delivery_terms": {
                    $model->getDeliveryTermsHelper()->setDeliveryTerms($value);
                } break;

                case "images": {
                    $model->setImages(!is_array($value) ? [$value] : $value);
                } break;

                case "images_order": {
                    $model->setImagesOrder(!is_array($value) ? [$value] : $value);
                } break;

                case "vocabulary": {
                    $model->getVocabularyHelper()->setProductVocabularyData($value);
                } break;

                case "place": {
                    $model->getPlaceHelper()->setData($value);
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
    public function save(): bool {
        /** @var Product $model */
        $model = $this->getModel();
        $saved = $model->save();
        if ($saved === false) {
            // beforeSave может сгенерировать ошибку сохранения.
            $this->populateErrorsFromAR($model->getErrors());
        }
        return $saved;
    }
}