<?php

namespace backend\components\form\controls;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use common\libs\form\components\Control;
use common\libs\traits\RegisterJsScript;
use common\assets\JqueryUiAsset;

/**
 * Class Range
 * @package backend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Range extends Control {
    use RegisterJsScript;

    const RANGE_FROM_PROPERTY = 'from';
    const RANGE_TO_PROPERTY = 'to';

    /** @var float|null */
    private $from;
    /** @var float|null */
    private $to;
    /** @var float|null */
    private $step = 1;
    /** @var null|Label */
    private $label_control;

    private $double_range_start_value;
    private $double_range_end_value;

    private $is_double = false;

    protected function registerFormAssets() {
        JqueryUiAsset::register(\Yii::$app->getView());
    }

    /**
     * @return float
     * @throws Exception
     */
    public function getFrom() {
        if (null === $this->from) {
            throw new Exception('From need to be set in range');
        }
        return (float)$this->from;
    }

    /**
     * @return float
     * @throws Exception
     */
    public function getTo() {
        if (null === $this->to) {
            throw new Exception('To need to be set in range');
        }
        return (float)$this->to;
    }

    /**
     * @return float
     */
    public function getStep() {
        return (float)$this->step;
    }

    public function registerScripts() {
        $this->registerJsScript();
    }

    /**
     * @param float $from
     * @return $this
     */
    public function setFrom($from) {
       $this->from = (float)$from;
       return $this;
    }

    /**
     * @param float $to
     * @return $this
     */
    public function setTo($to) {
       $this->to = (float)$to;
       return $this;
    }

    /**
     * @param float $step
     * @return $this
     * @throws
     */
    public function setStep($step) {
        if ($this->step === 0) {
            throw new Exception('Step not equal to zero in range');
        }
       $this->step = (float)$step;
       return $this;
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

    /**
     * @return bool
     */
    public function isDouble(): bool {
        return $this->is_double;
    }

    protected function beforeRender() {
        parent::beforeRender();
        $this->registerJsScript();
        $this->setIsMultiple();
        $this->addClass('range-control')
            ->addClass('col-sm-8')
            ->addDataAttributes([
                'min' => $this->getFrom(),
                'max' => $this->getTo(),
                'step' => $this->getStep(),
            ]);
    }

    public function setValue($value) {

        if (is_array($value)) {
            $this->double_range_start_value = ArrayHelper::getValue($value, self::RANGE_FROM_PROPERTY, 0);
            $this->double_range_end_value = ArrayHelper::getValue($value, self::RANGE_TO_PROPERTY, 0);
        }

        // 2 разных значения - 2 точки в диапазоне.
        if (is_array($value)
            && ($this->double_range_start_value !== $this->double_range_end_value)
        ) {
            $this->is_double = true;
        }

        // значение не массив.
        if (!is_array($value)) {
            $value = [self::RANGE_FROM_PROPERTY => $value, self::RANGE_TO_PROPERTY => $value];
            $this->is_double = false;
        }

        // одно из значений null - 2 точки в диапазоне.
        if (is_array($value)
            && ($this->double_range_start_value === null || $this->double_range_end_value === null)) {
            $this->is_double = false;
            if ($this->double_range_start_value === null) {
                $this->double_range_start_value = $this->double_range_end_value;
            }
            if ($this->double_range_end_value === null) {
                $this->double_range_end_value = $this->double_range_start_value;
            }
        }
        return parent::setValue($value);
    }

    public function getValue() {
        $value = parent::getValue();
        if (null === $value) {
            return [self::RANGE_FROM_PROPERTY => 0, self::RANGE_TO_PROPERTY => 0];
        }
        return $value;
    }

    /**
     * @return \common\libs\form\components\Input
     */
    private function getHiddenInput(): \common\libs\form\components\Input {
        $control = (new \common\libs\form\components\Input())
            ->setName($this->getName())
            ->setType(\common\libs\form\components\Input::TYPE_HIDDEN);
        return $control;
    }

    /**
     * @return array
     */
    private function getHiddenControls(): array {
        $hidden_controls[self::RANGE_FROM_PROPERTY] = $this->getHiddenInput()
            ->setName($this->getName().'['.self::RANGE_FROM_PROPERTY.']')
            ->addClass('range-start')
            ->setValue($this->double_range_start_value)
            ->render();

        $hidden_controls[self::RANGE_TO_PROPERTY] = $this->getHiddenInput()
            ->setName($this->getName().'['.self::RANGE_TO_PROPERTY.']')
            ->addClass('range-end')
            ->setValue($this->double_range_end_value)
            ->render();
        return $hidden_controls;
    }

    /**
     * @return Checkbox
     */
    private function getIsDoubleCheckbox(): Checkbox {
        $control = (new Checkbox())
            ->setTitle('Диапазон ?')
            ->setChecked($this->is_double)
            ->setName('is_double');
        return $control;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render(): string {
        parent::render();
        $label = $this->getLabelControl();

        $control = Html::tag(
            'div', '', $this->getAttributes() + ['class' => $this->getClassString()]
        );
        $hidden_controls = $this->getHiddenControls();

        $start = $this->getValue()[self::RANGE_FROM_PROPERTY] ?? 0;
        $end = $this->getValue()[self::RANGE_TO_PROPERTY] ?? 0;

        $max = max($start, $end);
        $min = min($start, $end);
        $value_header = $this->isDouble() ? $min. ' - '. $max : $start;

        return $this->renderTemplate([
            'from'              => $this->getFrom(),
            'to'                => $this->getTo(),
            'label'             => $label !== null ? $label->render() : '',
            'control'           => $control,
            'is_double_checkbox'=> $this->getIsDoubleCheckbox()->render(),
            'hidden_control'    => implode('', $hidden_controls),
            'errors'            => $this->getErrors(),
            'errors_string'     => $this->getErrorsAsString(','),
            'value_header'      => $value_header,
            'min'               => $min,
            'max'               => $max,
        ]);
    }
}