<?php

namespace backend\components\form\controls;

use common\libs\form\components\Control;
use common\libs\traits\RegisterJsScript;

/**
 * Class Copy
 * @package backend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Copy extends Control {
    use RegisterJsScript;

    #region типы внутренних контролов для клонирования.
    const TYPE_INPUT = 1;
    #endregion

    /** базовый класс wrapper для контрола. */
    const BASE_CONTROL_WRAPPER_CLASS = 'copy-control';

    /** класс для всех компонентов коллекции. */
    const COLLECTION_CONTROL_CLASS = 'collection-control';

    /** @var int - тип контролов коллекции по умолчанию. */
    private $type = self::TYPE_INPUT;
    /** @var int - максимальное количество клонируемых элементов. */
    private $max_clone_count = 10;
    /** @var int - количество контролов коллекции по умолчанию. */
    private $count_controls = 1;

    /** @var null|Label */
    private $label_control;

    /** @var array - список контролов коллекции. */
    private $controls = [];

    /** @var bool - были ли уже проинициализированы контролы коллекции. */
    private $inside_controls_initialized = false;

    /** @var array - массив возможных типов контролов коллекции. */
    static $types = [
        self::TYPE_INPUT,
    ];

    /**
     * @param int $type
     * @return $this
     */
    public function setType(int $type) {
        if (in_array($type, static::$types)) {
            $this->type = $type;
        }
        return $this;
    }

    /**
     * @param int $max_clone_count
     * @return $this
     */
    public function setMaxCloneCount(int $max_clone_count) {
        $this->max_clone_count = $max_clone_count;
        return $this;
    }

    /**
     * @param int $count_controls
     * @return $this
     */
    public function setCountControls(int $count_controls) {
        $this->count_controls = $count_controls;
        return $this;
    }

    public function getValue() {
        $values = parent::getValue();
        if (empty($values)) {
            return [];
        }
        if (!is_array($values)) {
            return [$values];
        }
        $values = array_filter($values, function ($value) {
            return null !== $value;
        });
        return $values;
    }

    /**
     * @return $this
     */
    private function initControls() {
        if ($this->inside_controls_initialized) {
            return $this;
        }

        // по дефолту Copy контрол multiple.
        $this->setIsMultiple();

        $values = $this->getValue();
        $count_values = count($values);
        if ($this->count_controls < $count_values) {
            $this->count_controls = $count_values;
        }

        // тип input.
        if ($this->type === self::TYPE_INPUT) {
            for ($i = 0; $i < $this->count_controls; $i++) {
                $control = $this->getInput($values[$i] ?? null);
                $this->controls[$i] = $control;
            }
        }
        $this->inside_controls_initialized = true;
        return $this;
    }

    /**
     * Получение инпута для типа self::TYPE_INPUT
     * @param null|array|string $value
     * @return Input
     */
    protected function getInput($value = null): Input {
        return (new Input())
            ->setName($this->getName())
            ->setIsMultiple()
            ->setAttributes($this->getClientValidatorAttributes())
            ->addDataAttributes([
                'name' => $this->getName(),
            ])
            ->addClasses([
                self::COLLECTION_CONTROL_CLASS,
            ])
            ->setPlaceholder($this->getPlaceholder())
            ->setValidationTitle($this->getValidationTitle())
            // серверная валидация.
            ->setRules($this->getRules())
            ->setValue($value);
    }

    public function validate() {
        $this->initControls();
        $errors = [];
        /** @var Control $control */
        foreach ($this->controls as $iterator => $control) {
            $errors[$iterator] = $control->validate();
        }
        return array_filter($errors);
    }

    /**
     * @return int
     */
    public function getControlsCount(): int {
        return $this->count_controls;
    }

    /**
     * @return Label|null
     */
    public function getLabelControl() {
        if (empty($this->getTitle())) {
            return null;
        }
        if (null === $this->label_control) {
            $this->label_control = (new Label())
                ->setContent($this->getTitle());
        }
        return $this->label_control;
    }

    public function beforeRender() {
        parent::beforeRender();
        $this->registerJsScript();
        $this->addDataAttributes([
            'max-clone-count' => $this->max_clone_count,
        ]);
        $this->addClasses([
            self::BASE_CONTROL_WRAPPER_CLASS,
        ]);
        $this->initControls();
    }

    /**
     * @return array
     */
    private function getControls(): array {
        return $this->controls;
    }

    public function registerScripts() {
        $this->registerJsScript();
    }

    public function render(): string {
        parent::render();
        $label = $this->getLabelControl();

        $controls_rendered = [];
        /** @var Control $control */
        foreach ($this->getControls() as $iterator => $control) {
            $controls_rendered[$iterator] = $control->render();
            $controls_rendered[$iterator] = str_replace('class="form-group"', '', $controls_rendered[$iterator]);
        }
        return $this->renderTemplate([
            'controls'          => $controls_rendered,
            'controls_count'    => $this->getControlsCount(),
            'label'             => $label !== null ? $label->render() : '',
            'attributes_string' => $this->getAttributesString(),
            'classes_string'    => $this->getClassString(),
        ]);
    }
}