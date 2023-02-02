<?php

namespace backend\components;

use yii\base\Exception;

use backend\forms\product\ProductVocabulary as ProductVocabularyFormBackend;
use frontend\modules\cabinet\forms\product\ProductVocabulary as ProductVocabularyFormFrontend;

use common\models\Vocabulary;
use common\models\CategoryVocabulary;
use common\models\goods\Product;
use common\libs\traits\Singleton;

/**
 * Класс собирает все характеристики и их значения по категории и товару,
 * приводит их к необходимому виду и рендерит форму характеристик для товара.
 * Используется при редактировании / добавлении нового товара.
 * TODO: перенести в common.
 * @see \backend\forms\product\ProductVocabulary
 * Class ProductVocabularyProcessor
 * @package backend\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class ProductVocabularyProcessor {
    use Singleton;

    const ENVELOPE_FRONTEND = 1;
    const ENVELOPE_BACKEND = 2;

    const VOCABULARY_KEY = 'vocabulary';
    const VOCABULARY_OPTIONS = 'options';
    const VOCABULARY_OPTIONS_ARRAY = 'options_array';
    const VOCABULARY_CATEGORY_RELATION_INFO = 'vocabulary_category_relation_info';
    const VOCABULARY_PRODUCT_VALUE = 'vocabulary_product_value';

    /** @var null|int */
    private $category_id;
    /** @var null|int */
    private $product_id;
    /** @var null|int */
    private $envelope;

    /**
     * @param int $category_id
     * @return $this
     */
    public function setCategoryId($category_id) {
        $this->category_id = (int)$category_id;
        return $this;
    }

    /**
     * @param int $product_id
     * @return $this
     */
    public function setProductId($product_id) {
        $this->product_id = (int)$product_id;
        return $this;
    }

    /**
     * @return null|Product
     */
    public function getProductModel() {
        if (null !== $this->product_id) {
            return Product::getByIdCached((int)$this->product_id);
        }
        return null;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getCategoryId() {
        if (null === $this->category_id) {
            throw new Exception('Category id must be set.');
        }
        return (int)$this->category_id;
    }

    /**
     * Собирает данные характеристик для формы.
     * @return array
     */
    private function prepareDataForForm() {
        $prepared_data = [];
        $values = [];
        $product = $this->getProductModel();
        if (null !== $product) {
            $values = $product->getVocabularyHelper()->getValues();
        }
        $vocabulary_relation_data = CategoryVocabulary::getVocabularyDataByCategory($this->getCategoryId());
        foreach ($vocabulary_relation_data as $vocabulary_data) {
            /** @var Vocabulary $vocabulary */
            $vocabulary = Vocabulary::findOne($vocabulary_data['vocabulary_id'] ?? 0);
            $prepared_data[] = [
                self::VOCABULARY_KEY => $vocabulary,
                self::VOCABULARY_OPTIONS => $vocabulary->getVocabularyOptions($this->getCategoryId())->all(),
                self::VOCABULARY_OPTIONS_ARRAY => $vocabulary->getOptionsArray($this->getCategoryId()),
                // значение характеристики товара.
                self::VOCABULARY_PRODUCT_VALUE => $values[$vocabulary->id] ?? null,
                self::VOCABULARY_CATEGORY_RELATION_INFO => $vocabulary_data,
            ];
        }
        return $prepared_data;
    }

    /**
     * @param int $envelope
     * @return $this
     */
    public function setEnvelope(int $envelope) {
        $this->envelope = $envelope;
        return $this;
    }

    /**
     * Рендеринг формы характеристик товара (категории).
     * @return string
     */
    public function renderVocabularyTerms() {
        if ($this->envelope === self::ENVELOPE_BACKEND) {
            return (new ProductVocabularyFormBackend())
                ->setVocabularyOptionsData($this->prepareDataForForm())
                ->render();
        }
        if ($this->envelope === self::ENVELOPE_FRONTEND) {
            return (new ProductVocabularyFormFrontend())
                ->setVocabularyOptionsData($this->prepareDataForForm())
                ->render();
        }
        return null;
    }
}