<?php

namespace common\libs\form\traits;

use common\libs\form\DynamicModel;
use yii\helpers\ArrayHelper;

/**
 * Class ValidatorTrait
 * @package common\libs\form\traits
 * @author Артём Широких kowapssupport@gmail.com
 */
trait ValidatorTrait {

    /** @var array - Ошибки. */
    private $errors = [];

    /** @var array - Правила валидации. */
    private $validators = [];

    /** @var null|DynamicModel */
    private $dynamic_model;

    /** @var null|string - подстанавливается в сообщение ошибки валидации. */
    private $validation_title;

    /**
     * @return array
     */
    public function getRules(): array {
        return $this->validators;
    }

    /**
     * Устанавливает правила валидации.
     * @see Class \yii\base\DynamicModel
     * @param array $rule
     * @return $this
     */
    public function addRule(array $rule) {
        if (!empty($rule)) {
            $this->validators[] = $rule;
        }
        return $this;
    }

    /**
     * @param array $rules
     * @return $this
     */
    public function setRules(array $rules = []) {
        $this->validators = $rules;
        return $this;
    }

    /**
     * @see \common\libs\form\DynamicModel::setLabels
     * @return string
     */
    protected function getValidationTitle(): string {
        if (null === $this->validation_title) {
            return $this->getTitle();
        }
        return (string)$this->validation_title;
    }

    /**
     * @param string $validation_title
     * @return $this
     */
    public function setValidationTitle(string $validation_title) {
        $this->validation_title = $validation_title;
        return $this;
    }

    /**
     * Валидирует value в соответствии с правилами валидации.
     * @return array
     */
    public function validate() {

        $dynamic_model = $this->getDynamicModel();

        foreach ($this->validators as $validator) {
            $validator_name = ArrayHelper::getValue($validator, 0, false);
            if (!$validator_name) {
                continue;
            }
            $validator_params = array_slice($validator, 1);
            $dynamic_model->addRule(
                $this->getName(),
                $validator_name,
                $validator_params
            );
            $dynamic_model->setLabels([$this->getName() => $this->getValidationTitle()]);
        }

        if (!$dynamic_model->validate()) {
            $errors = ArrayHelper::getValue($dynamic_model->getErrors(), $this->getName(), []);
            foreach ($errors as $error) {
                $this->addError($error);
            }
        }

        return $this->getErrors();
    }

    /**
     * @return bool
     */
    public function isValid() {
        return empty($this->getDynamicModel()->getErrors());
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return null|DynamicModel
     */
    public function getDynamicModel() {
        if (is_null($this->dynamic_model)) {
            $this->dynamic_model = new DynamicModel([
                $this->getName() => $this->getValue(),
            ]);
        }
        return $this->dynamic_model;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function addError($error) {
        $this->errors[] = (string)$error;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearErrors() {
        $this->errors = [];
        return $this;
    }

    /**
     * Возвращает список ошибок через разделитель.
     * @param string $comma
     * @return string
     */
    public function getErrorsAsString(string $comma): string {
        return implode($comma, $this->getErrors());
    }

    // методы которые переопределяются в компоненте.
    public function getName() {return '';}
    public function getValue() {return '';}
    public function getTitle() {return '';}
}