<?php

namespace frontend\modules\cabinet\forms\product;

use yii\helpers\ArrayHelper;

use common\models\firms\Firm;
use common\models\goods\Product;
use common\libs\form\Form;

use frontend\models\form\Join;
use frontend\components\form\controls\Selectize;
use frontend\components\form\controls\Delivery;
use frontend\components\form\controls\Image;
use frontend\components\form\controls\Price;
use frontend\components\form\controls\Specification;
use frontend\components\form\controls\TextArea;
use frontend\components\form\controls\Capacity;

use backend\forms\product\Update;

/**
 * Class Add
 * Форма добавления одиночного товара из кабинета.
 * @package frontend\modules\cabinet\forms\product
 * @author Артём Широких kowapssupport@gmail.com
 */
class Add extends Form {

    #region контролы для добавления товара.
    /** @var null|Selectize */
    private $product_category_control = null;
    /** @var null|Image */
    private $product_image_control = null;
    /** @var null|Delivery */
    private $product_delivery_control = null;
    /** @var null|Price */
    private $product_price_control = null;
    /** @var null|Capacity */
    private $product_capacity_control = null;
    /** @var null|Specification */
    private $product_specification_control = null;
    /** @var null|TextArea */
    private $product_description_control = null;
    #endregion;

    protected function initControls(): void {

        $this->addClass('cabinet-product-add-form');
        $this->addTemplateVars(['is_update_product' => !$this->getModel()->isNewRecord]);

        // категория товара.
        $this->registerControl($this->getProductCategoryControl());
        // фото товара.
        $this->registerControl($this->getProductImageControl());
        // цена товара.
        $this->registerControl($this->getProductPriceControl());
        // мощности.
        $this->registerControl($this->getProductCapacityControl());
        // условия доставки товара.
        $this->registerControl($this->getProductDeliveryControl());
        // описание товара.
        $this->registerControl($this->getProductDescriptionControl());
    }

    /**
     * @return Selectize
     */
    public function getProductCategoryControl(): Selectize {
        if (!$this->product_category_control) {
            /*
            $this->product_category_control = (new Activity())
                ->setIsProductCategoryState()
                ->addClass('product-category-control')
                ->setName('category')
                ->setTitle('Категория товара')
                //->setLabelTip('У товара может быть не более 3-х категорий')
                ->setPlaceholder('Введите название товара');*/

            // временное решение.
            $category_control = (new Selectize())
                ->setName('category')
                ->setTitle('Категория товара')
                ->setPlaceholder('Выберите конечную категорию товара')
            ;
            (new Update())->populateCategories($category_control);
            return $this->product_category_control = $category_control;
        }
        return $this->product_category_control;
    }

    /**
     * @return Image
     */
    public function getProductImageControl(): Image {
        if (!$this->product_image_control) {
            $this->product_image_control = (new Image())
                ->setName('image')
                ->setTitle('Фото товара')
                ->setMultiple();
        }
        return $this->product_image_control;
    }

    /**
     * @return Delivery
     */
    public function getProductDeliveryControl(): Delivery {
        if (!$this->product_delivery_control) {
            $this->product_delivery_control = (new Delivery())
                ->setTitle('Условия доставки')
                ->setName('delivery_terms');
        }
        return $this->product_delivery_control;
    }

    /**
     * @return Price
     */
    public function getProductPriceControl(): Price {
        if (!$this->product_price_control) {
            $this->product_price_control = (new Price())
                ->setName('price')
                ->setTitle('Ориентировочная цена');
        }
        return $this->product_price_control;
    }

    /**
     * @return Capacity
     */
    public function getProductCapacityControl(): Capacity {
        if (!$this->product_capacity_control) {
            $this->product_capacity_control = (new Capacity())
                ->setName('capacity')
                ->setTitle('Мощности');
        }
        return $this->product_capacity_control;
    }

    /**
     * @return TextArea
     */
    public function getProductDescriptionControl(): TextArea {
        if (!$this->product_description_control) {
            $this->product_description_control = (new TextArea())
                ->setName('description')
                ->setTitle('Описание товара')
                ->setPlaceholder('Введите описание вашего товара')
                ->addRule(['required']);
        }
        return $this->product_description_control;
    }

    /**
     * @return Specification
     */
    public function getProductSpecificationControl(): Specification {
        if (!$this->product_specification_control) {
            $this->product_specification_control = (new Specification())
                ->setName('specification')
                ->setTitle('Технические характеристики');
        }
        return $this->product_specification_control;
    }

    /**
     * Заполнение модели данными из формы.
     * @throws \yii\base\Exception
     */
    protected function populateModel(): void {
        $controls_data = $this->getControlsData();

        /** @var Product $model */
        $model = $this->getModel();
        $model->setAttributes($controls_data);

        // мощности.
        $capacity_from = ArrayHelper::getValue($controls_data, 'capacity.'.Capacity::KEY_FROM, 0);
        $capacity_to = ArrayHelper::getValue($controls_data, 'capacity.'.Capacity::KEY_TO, 0);

        // категории.
        $category_id = ArrayHelper::getValue($controls_data, 'category', 0);
        $model->setCategories([$category_id]);

        // условия доставки.
        $delivery_terms = ArrayHelper::getValue($controls_data, 'delivery_terms', []);
        if (is_array($delivery_terms)) {
            $model->getDeliveryTermsHelper()->setDeliveryTerms($delivery_terms);
        }

        // фото.
        $images = ArrayHelper::getValue($controls_data, 'image', []);
        if (is_array($images)) {
            $model->setImages($images);
        }

        // характеристики.
        $vocabularies = ArrayHelper::getValue($controls_data, 'vocabulary', []);
        if (!empty($vocabularies)) {
            $model->getVocabularyHelper()->setProductVocabularyData($vocabularies);
        }

        $model->setUserId((new Join())->getUserId())
            ->setFirmId(Firm::get()->id)
            ->setPrice(ArrayHelper::getValue($controls_data, 'price.'.Price::PRICE_NAME, 0))
            ->setUnitId(ArrayHelper::getValue($controls_data, 'price.'.Price::UNIT_NAME, 0))
            ->setCapacity($capacity_from, $capacity_to)
            ->setText(ArrayHelper::getValue($controls_data, 'description', ''))
            // товар пользователя на модерации.
            ->setStatus(Product::STATUS_PRODUCT_MODERATION)
        ;
    }

    public function validate(): array {

        $this->populateModel();
        $this->getModel()->validate();
        $this->populateErrorsFromAR($this->getModel()->getErrors());

        $category = $this->getControlData('category');
        if (empty($category)) {
            $this->addError('category', 'Выберите по крайней мере 1 категорию товара.');
        }
        $image = $this->getControlData('image');
        if (empty($image)) {
            $this->addError('image', 'Загрузите по крайней мере 1 фотографию товара.');
        }

        $from_capacity = (int)ArrayHelper::getValue($this->getControlData('capacity'), Capacity::KEY_FROM, 0);
        $to_capacity = (int)ArrayHelper::getValue($this->getControlData('capacity'), Capacity::KEY_TO, 0);

        if ($from_capacity > $to_capacity) {
            $this->addError('capacity', 'Введите корректный диапазон мощностей.');
        }
        return parent::validate();
    }

    public function save(): bool {
        return $this->getModel()->save();
    }
}