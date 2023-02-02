<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Control;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Контрол для работы характеристик товара.
 * TODO: необходимо переписать скрипт.
 * Class Specification
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class SpecificationOld extends Control {

    const TECH_SPECS_KEY = 'tech_specs_key';
    const TECH_SPECS_VALUES = 'tech_specs_value';

    private $spec_key_control = null;
    private $spec_value_control = null;

    /**
     * @return Input
     */
    public function getSpecKeyControl() {
        if (is_null($this->spec_key_control)) {
            $this->spec_key_control = (new Input())
                ->setName(self::TECH_SPECS_KEY)
                ->setIsMultiple()
                ->setType(Input::TYPE_HIDDEN);
        }
        return $this->spec_key_control;
    }

    /**
     * @return Input
     */
    public function getSpecValueControl() {
        if (is_null($this->spec_value_control)) {
            $this->spec_value_control = (new Input())
                ->setName(self::TECH_SPECS_VALUES)
                ->setIsMultiple()
                ->setType(Input::TYPE_HIDDEN);
        }
        return $this->spec_value_control;
    }

    /**
     * Возвращает значения в необходимом формате.
     */
    public function getValue() {

        $keys = $this->getSpecKeyControl()->getValue();
        $values = $this->getSpecValueControl()->getValue();

        return $this->getPreparedVocabularyTerms([
            self::TECH_SPECS_KEY    => is_null($keys) ? [] : $keys,
            self::TECH_SPECS_VALUES => is_null($values) ? [] : $values,
        ]);
    }

    /**
     * Метод подготавливает данные с hidden input в массив.
     * @param array $value
     * @return array
     */
    private function getPreparedVocabularyTerms($value) {

        $tech_specs_key = ArrayHelper::getValue($value, self::TECH_SPECS_KEY, []);
        $tech_specs_value = ArrayHelper::getValue($value, self::TECH_SPECS_VALUES, []);

        $value = [];

        try {
            foreach ($tech_specs_key as $iterator => $vocabulary_json) {
                $vocabulary_data = Json::decode($vocabulary_json);
                $vocabulary = ['name' => ArrayHelper::getValue($vocabulary_data, 'name', '')];
                if ($vocabulary_id = ArrayHelper::getValue($vocabulary_data, 'id', 0)) {
                    $vocabulary['id'] = intval($vocabulary_id);
                }
                // данные словарей.
                $value[$iterator] = [
                    'vocabulary' => $vocabulary,
                ];
                // значения словарей.
                $terms_json = ArrayHelper::getValue($tech_specs_value, $iterator);
                $terms = [];
                foreach ($terms_json as $term_iterator => $term_json) {
                    $term = Json::decode($term_json);
                    if ($term_id = ArrayHelper::getValue($term, 'id', 0)) {
                        $terms[$term_iterator] = ['id' => intval($term_id)];
                    }
                    $terms[$term_iterator]['name'] = ArrayHelper::getValue($term, 'name', '');
                }
                $value[$iterator]['terms'] = $terms;
            }
            return $value;
        }
        catch (Exception $exception) {
            return [];
        }
    }

    public function render(): string {
        return $this->renderTemplate();
    }
}