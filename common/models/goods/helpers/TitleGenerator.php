<?php

namespace common\models\goods\helpers;

use yii\helpers\ArrayHelper;

use common\libs\StringHelper;
use common\models\Category;
use common\models\CategoryVocabulary;

/**
 * Class TitleGenerator
 * todo: подставлять единицу измерения.
 * @package common\models\goods\helpers
 * @author Артём Широких kowapssupport@gmail.com
 */
class TitleGenerator extends Base {

    /** @var null|string */
    private $product_title;

    /** @var string  */
    private $vocabulary_name_value_separator = ': ';
    /** @var string  */
    private $vocabulary_separator = ', ';
    /** @var string  */
    private $vocabulary_between_values_separator = ', ';

    /**
     * @return string
     */
    public function getGeneratedTitle(): string {
        if (null === $this->product_title) {
            return $this->product_title = $this->generate();
        }
        return $this->product_title;
    }

    /**
     * @return bool
     */
    public function updateProductTitle(): bool {
        $product = $this->getProductModel();
        $product->title = $this->getGeneratedTitle();
        return $product->save(false, ['title']);
    }

    /**
     * @return string
     */
    private function generate(): string {
        $product = $this->getProductModel();
        if ($product->isNewRecord) {
            return $this;
        }
        $product_name_parts = [];
        /** @var Category $category */
        $category = $product->getCategories()->one();
        if (null !== $category) {
            $product_name_parts[] = $category->title;
        }

        $product_vocabulary_values = $product->getVocabularyHelper()->getValues();
        $product_category_id = $product->getCategoryId();

        if (null === $product_category_id) {
            return '';
        }

        $category_vocabulary_data = CategoryVocabulary::getVocabularyDataByCategory($product_category_id);

        foreach ($category_vocabulary_data as $iterator => $category_vocabulary_info) {

            $type = ArrayHelper::getValue($category_vocabulary_info, 'type', 0);
            $use_in_product_name = ArrayHelper::getValue($category_vocabulary_info, 'use_in_product_name', false);
            $use_only_vocabulary_name = ArrayHelper::getValue($category_vocabulary_info, 'use_only_vocabulary_name', false);
            $use_only_vocabulary_value = ArrayHelper::getValue($category_vocabulary_info, 'use_only_vocabulary_value', false);

            // использовать и название характеристики и значение.
            $is_use_vocabulary_name_and_value = (
                $use_in_product_name
                && !$use_only_vocabulary_name
                && !$use_only_vocabulary_value
            );

            // магия которая определяет использовать название либо значение.
            $use_only_vocabulary_name = $use_only_vocabulary_name || $is_use_vocabulary_name_and_value;
            $use_only_vocabulary_value = $use_only_vocabulary_value || $is_use_vocabulary_name_and_value;

            $vocabulary_part = '';
            if ($use_in_product_name) {

                // значение характеристики.
                $value = $product_vocabulary_values[(int)$category_vocabulary_info['id']] ?? null;
                $decorated_value = $this->decorateVocabularyPart($type, $value);

                // используется только название характеристики.
                if ($use_only_vocabulary_name) {
                    $vocabulary_part = ArrayHelper::getValue($category_vocabulary_info, 'title');
                    if ($use_only_vocabulary_value) {

                        if (!empty($decorated_value)) {
                            $vocabulary_part .= $this->vocabulary_name_value_separator;
                        }
                    }
                }

                // используется только значение характеристики.
                if ($use_only_vocabulary_value) {
                    if (!empty($decorated_value)) {
                        $vocabulary_part .= $decorated_value;
                    }
                }

                if (!empty($vocabulary_part)) {
                    $product_name_parts[] = $vocabulary_part;
                }
            }
        }

        $imploded = implode($this->vocabulary_separator, $product_name_parts);
        $imploded = mb_strtolower($imploded);
        $imploded = StringHelper::firstUpperLetter($imploded);

        return $imploded;
    }

    /**
     * Декорирует вывод значения характеристик.
     * @param int $type
     * @param mixed $value
     * @return string
     */
    private function decorateVocabularyPart(int $type, $value): string {
        // значение.
        if ($type === \common\models\Vocabulary::TYPE_VALUE) {
            return (string)$value;
        }
        // выбор значения.
        if ($type === \common\models\Vocabulary::TYPE_SELECT) {
            if ($value !== null && is_array($value)) {
                return implode($this->vocabulary_between_values_separator, $value);
            }
        }
        // текст.
        if ($type === \common\models\Vocabulary::TYPE_TEXT) {
            return (string)$value;
        }
        // диапазон.
        if ($type === \common\models\Vocabulary::TYPE_RANGE) {
            if ($value !== null && is_array($value)) {
                $start = min($value);
                $end = max($value);
                if ($start === $end) {
                    return $start;
                }
                return $start. ' - '. $end;
            }
        }
        return '';
    }

    /**
     * Разделитель между названием характеристики и значением.
     * @param string $separator
     * @return $this
     */
    public function setVocabularyNameValueSeparator(string $separator) {
        $this->vocabulary_name_value_separator = $separator;
        return $this;
    }

    /**
     * Разделитель между характеристиками.
     * @param string $separator
     * @return $this
     */
    public function setVocabularySeparator(string $separator) {
        $this->vocabulary_separator = $separator;
        return $this;
    }

    /**
     * @param string $separator
     * @return $this
     */
    public function setVocabularyBetweenValuesSeparator(string $separator) {
        $this->vocabulary_between_values_separator = $separator;
        return $this;
    }
}