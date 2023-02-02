<?php

namespace backend\components;

use common\models\goods\Product;
use yii\helpers\ArrayHelper;

/**
 * Class PriceProcessor
 * Класс получает данные с PriceList.js и сохраняет товарные позиции.
 * @package backend\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class PriceProcessor {

    const PARAM_FIRM_ID         = 'firm_id';
    const PARAM_TITLE           = 'title';
    const PARAM_PRICE           = 'price';
    const PARAM_CATEGORY        = 'category';
    const PARAM_VOCABULARIES    = 'vocabularies';
    const PARAM_IMAGES          = 'images';

    /** @var array  */
    private $params = [];

    public function process() {
        // сохранение товара.
        $product = (new Product())
            ->setPrice($this->getPrice())
            ->setFromImport()
            ->setFirmId($this->getFirmId())
            // товар после импорта на модерации.
            ->setStatus(Product::STATUS_PRODUCT_MODERATION)
        ;
        // категория.
        if ($category = $this->getCategory()) {
            $product->setCategories([$category]);
        }
        // фото товара.
        if ($images = $this->getImages()) {
            $product->setImages(ArrayHelper::getColumn($images, 'path'));
        }
        // характеристики товара.
        if ($vocabularies = $this->getVocabularies()) {
            $product->setIsActiveCreateVocabulary();
            $product->setIsActiveCreateTerm();
            $product->setVocabularyTerms($this->getPreparedVocabularyTerms($vocabularies));
        }

        $product->save();
    }

    /**
     * Метод приводит данные характеристик к нужному формату для сохранения.
     * @param array $vocabulary_terms
     * @return array
     */
    private function getPreparedVocabularyTerms(array $vocabulary_terms) {
        $out_data = [];
        foreach ($vocabulary_terms as $key => $vocabulary) {
            // название характеристики.
            $v_title = ArrayHelper::getValue($vocabulary, 'string_title');
            if (empty($v_title)) continue;
            // значения характеристик.
            $v_terms = ArrayHelper::getValue($vocabulary, 'terms');
            // данные на выход.
            $out_data[$key] = [
                'vocabulary' => ['name' => $v_title],
                'terms' => [],
            ];
            if ($v_terms && is_array($v_terms)) {
                foreach ($v_terms as $term) {
                    $t_value = ArrayHelper::getValue($term, 'string_title');
                    if (!$t_value) continue;
                    $out_data[$key]['terms'][] = ['name' => $t_value];
                }
            }
        }
        return $out_data;
    }

    /**
     * @param integer $firm_id
     * @return $this
     */
    public function setFirmId($firm_id) {
        $this->params[self::PARAM_FIRM_ID] = (int)$firm_id;
        return $this;
    }

    /**
     * @return integer|null
     */
    public function getFirmId() {
        return (isset($this->params[self::PARAM_FIRM_ID]))
            ? $this->params[self::PARAM_FIRM_ID] : null;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title) {
        $this->params[self::PARAM_TITLE] = (string)$title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle() {
        return (isset($this->params[self::PARAM_TITLE]))
            ? $this->params[self::PARAM_TITLE] : null;
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice($price) {
        $this->params[self::PARAM_PRICE] = floatval($price);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice() {
        return (isset($this->params[self::PARAM_PRICE]))
            ? $this->params[self::PARAM_PRICE] : null;
    }

    /**
     * @param integer
     * @return $this
     */
    public function setCategory($category) {
        $this->params[self::PARAM_CATEGORY] = (int)$category;
        return $this;
    }

    /**
     * @return integer
     */
    public function getCategory() {
        return (isset($this->params[self::PARAM_CATEGORY]))
            ? $this->params[self::PARAM_CATEGORY] : 0;
    }

    /**
     * @param array $vocabularies
     * @return $this
     */
    public function setVocabularies(array $vocabularies) {
        $this->params[self::PARAM_VOCABULARIES] = $vocabularies;
        return $this;
    }

    /**
     * @return array
     */
    public function getVocabularies() {
        return (isset($this->params[self::PARAM_VOCABULARIES]))
            ? $this->params[self::PARAM_VOCABULARIES] : [];
    }

    /**
     * @param array $images
     * @return $this
     */
    public function setImages(array $images) {
        $this->params[self::PARAM_IMAGES] = $images;
        return $this;
    }

    /**
     * @return array
     */
    public function getImages() {
        return (isset($this->params[self::PARAM_IMAGES]))
            ? $this->params[self::PARAM_IMAGES] : [];
    }
}