<?php

namespace backend\forms\product;

use backend\components\form\controls\Input;
use backend\components\form\controls\Range;
use backend\components\form\controls\Selectize;
use backend\components\form\Form;
use backend\components\ProductVocabularyProcessor;
use common\models\Vocabulary;
use common\models\VocabularyOption;

/**
 * Class ProductVocabulary
 * @package backend\forms\product
 * @author Артём Широких kowapssupport@gmail.com
 */
class ProductVocabulary extends Form
{

    const VOCABULARY_CONTROL_NAME_PREFIX = 'vocabulary';

    /** @var array - данные по характеристикам товара. */
    private $vocabulary_options_data = [];

    protected function initControls(): void {
        parent::initControls();

        $this->setControlsTemplateEnv(static::MODE_USE_BACKEND_CONTROLS_TEMPLATE)
            ->needRenderFormTags(false);

        foreach ($this->vocabulary_options_data as $vocabulary_options_data) {

            /** @var Vocabulary $vocabulary */
            $vocabulary = $vocabulary_options_data[ProductVocabularyProcessor::VOCABULARY_KEY];
            /** @var VocabularyOption[] $terms */
            $options_array = $vocabulary_options_data[ProductVocabularyProcessor::VOCABULARY_OPTIONS_ARRAY];
            /** @var mixed $product_vocabulary_value - значение характеристики товара. */
            $product_vocabulary_value = $vocabulary_options_data[ProductVocabularyProcessor::VOCABULARY_PRODUCT_VALUE];
            /** @var array $category_vocabulary_data */
            $category_vocabulary_data = $vocabulary_options_data[ProductVocabularyProcessor::VOCABULARY_CATEGORY_RELATION_INFO];

            $control_name = $this->getNameForControl($vocabulary);

            switch ($vocabulary->getType()) {

                // тип "значение".
                case Vocabulary::TYPE_VALUE: {

                    $value_control = (new Input())
                        ->setTitle($vocabulary->getTitle())
                        ->setName($control_name)
                        ->setType(Input::TYPE_NUMBER)
                    ;
                    if ($vocabulary->isMultipleValueType()) {
                        $value_control->setIsMultiple(true);
                    }
                    if (null !== $product_vocabulary_value) {
                        $value_control->setValue($product_vocabulary_value);
                    }
                    $this->registerControl($value_control);

                } break;

                // тип "диапазон значений".
                case Vocabulary::TYPE_RANGE: {

                    $range_control = new Range();
                    $range_control
                        ->setTitle($vocabulary->getTitle())
                        ->setName($control_name)
                        ->setFrom($category_vocabulary_data['range_from'] ?? 0)
                        ->setTo($category_vocabulary_data['range_to'] ?? 0)
                        ->setStep($category_vocabulary_data['range_step'] ?? 0)
                    ;

                    if (null !== $product_vocabulary_value) {
                        $range_control->setValue([
                            Range::RANGE_FROM_PROPERTY => $product_vocabulary_value[0] ?? null,
                            Range::RANGE_TO_PROPERTY => $product_vocabulary_value[1] ?? null,
                        ]);
                    }
                    $this->registerControl($range_control);

                } break;

                // тип "выбор предопределённых значений".
                case Vocabulary::TYPE_SELECT: {

                    $select_control = (new Selectize())
                        ->setTitle($vocabulary->getTitle())
                        ->setName($control_name)
                        ->setArrayOfOptions($options_array)
                        ->setIsMultiple($vocabulary->isMultipleValueType())
                    ;
                    if (null !== $product_vocabulary_value) {
                        $select_control->setValue(array_keys($product_vocabulary_value));
                    }
                    $this->registerControl($select_control);

                } break;

                // тип "произвольное значение".
                case Vocabulary::TYPE_TEXT: {

                    $value_control = (new Input())
                        ->setTitle($vocabulary->getTitle())
                        ->setName($control_name)
                    ;
                    if ($vocabulary->isMultipleValueType()) {
                        $value_control->setIsMultiple(true);
                    }
                    if (null !== $product_vocabulary_value) {
                        $value_control->setValue($product_vocabulary_value);
                    }
                    $this->registerControl($value_control);
                }
            }
        }
    }

    public function loadResources(): static
    {
        //TODO: проверить на необходимость регистрировать скрипт Range / Selectize.
        $range_control = new Range();
        $range_control->registerScripts();

        $selectize_control = new Selectize();
        $selectize_control->registerScripts();

        return $this;
    }

    /**
     * Возвращает name атрибут для контрола характеристики.
     * @param Vocabulary $vocabulary
     * @return string
     */
    private function getNameForControl(Vocabulary $vocabulary) {
        return self::VOCABULARY_CONTROL_NAME_PREFIX. '['. $vocabulary->id. ']';
    }

    /**
     * Устанавливает данные характеристик и их значений - товара.
     * @param array $data
     * @return $this
     */
    public function setVocabularyOptionsData(array $data) {
        $this->vocabulary_options_data = $data;
        return $this;
    }
}