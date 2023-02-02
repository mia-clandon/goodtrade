<?php

namespace backend\forms\category;

use backend\components\CatalogProcessor;
use backend\components\form\controls\{Button, Copy, Input, Select, Selectize};
use backend\components\form\Form;
use common\libs\form\components\Option;
use common\libs\form\validators\client\Required;
use common\models\{Category, CategoryVocabulary, Unit, Vocabulary};

/**
 * Class VocabularyRelation
 * @property integer $vocabulary_id
 * @property integer $category_id
 * @property bool $for_table_mode
 * @property integer $unit_code
 * @property float $range_from
 * @property float $range_to
 * @property float $range_step
 * @property array $options
 * @package backend\forms\category
 * @author Артём Широких kowapssupport@gmail.com
 */
class VocabularyRelation extends Form
{

    /** @var Input|null */
    private $range_from_control;
    /** @var Input|null */
    private $range_to_control;
    /** @var Input|null */
    private $range_step_control;

    /** @var Copy|null */
    private $vocabulary_options_control;

    private $for_table_mode = false;

    /** @var null|Category */
    private $category_model;
    /** @var null|Vocabulary */
    private $vocabulary_model;

    protected function initControls(): void
    {
        parent::initControls();

        $vocabulary_model = $this->getVocabularyModel();
        $category_vocabulary_model = $this->getModel();

        if (!$category_vocabulary_model->isNewRecord) {
            $this->addClass('update-form');
        }
        if ($this->for_table_mode) {
            $this->addClass('vocabulary-table-form-mode');
        }

        $this->setId('vocabulary-relation-form');
        $this->setTemplateFileName('vocabulary_relation');

        // выбор характеристики.
        $vocabulary_control = (new Select())
            ->setName('vocabulary_id')
            ->setTitle('Выберите характеристику');
        $this->populateVocabularyControl($vocabulary_control);
        if (!$category_vocabulary_model->isNewRecord) {
            //todo: временно не даю менять характеристику.
            $vocabulary_control->setDisabled();
        }
        $this->registerControl($vocabulary_control);

        // единица измерения.
        $units_control = (new Selectize())
            ->setName('unit_code')
            ->setTitle('Выберите единицу измерения')
            ->setCreateProperty(false)
            ->setSortField('text')
            ->allowEmptyOption()
            ->setPersist(false);
        $this->populateUnitControl($units_control);
        $this->registerControl($units_control);

        $category_id_control = (new Input())
            ->setType(Input::TYPE_HIDDEN)
            ->setName('category_id');
        if ($this->category_id) {
            $category_id_control->setValue($this->category_id);
        }
        $this->registerControl($category_id_control);

        # настройка диапазона.
        $this->range_from_control = (new Input())
            ->setName('range_from')
            ->setType(Input::TYPE_NUMBER)
            ->addRule(['required'])
            ->addRule(['number'])
            ->setTitle('Начало диапазона')
            ->addAttributes(['step' => 'any'])
            ->setValue(0);
        if (!$category_vocabulary_model->isNewRecord) {
            $this->range_from_control->setValue($category_vocabulary_model->getRangeFrom());
        }
        $this->registerControl($this->range_from_control);

        $this->range_to_control = (new Input())
            ->setName('range_to')
            ->setType(Input::TYPE_NUMBER)
            ->addRule(['required'])
            ->addRule(['number'])
            ->setTitle('Конец диапазона')
            ->addAttributes(['step' => 'any'])
            ->setValue(0);
        if (!$category_vocabulary_model->isNewRecord) {
            $this->range_to_control->setValue($category_vocabulary_model->getRangeTo());
        }
        $this->registerControl($this->range_to_control);

        $this->range_step_control = (new Input())
            ->setName('range_step')
            ->setType(Input::TYPE_NUMBER)
            ->addRule(['required'])
            ->addRule(['number'])
            ->setTitle('Шаг диапазона')
            ->addAttributes(['step' => 'any'])
            ->setValue(0);
        if (!$category_vocabulary_model->isNewRecord) {
            $this->range_step_control->setValue($category_vocabulary_model->getRangeStep());
        }
        $this->registerControl($this->range_step_control);

        $this->vocabulary_options_control = (new Copy())
            ->setName('options')
            ->setTitle('Значение характеристики')
            ->setValidationTitle('Значение характеристики')
            ->addRule(['required'])
            ->setJsValidator([new Required()])
            ->setMaxCloneCount(100)
            ->setPlaceholder('Новое значение характеристики ...');
        if (null !== $vocabulary_model) {
            $options = $vocabulary_model->getOptionsArray((int)$this->category_id);
            $this->vocabulary_options_control->setValue(array_values($options));
        }
        $this->registerControl($this->vocabulary_options_control);

        // кнопки действий.
        $submit_control = (new Button())
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
            ->setName('submit')
            ->setContent('Сохранить')
            ->setType(Button::TYPE_SUBMIT);
        $this->registerControl($submit_control);
    }

    /**
     * Метод заполняет контрол выбора характеристики значениями.
     * @param Select $control
     * @return $this
     */
    private function populateVocabularyControl(Select $control)
    {
        if ($this->getCategoryModel() === null) {
            return $this;
        }
        $possible_vocabularies = (new Vocabulary())
            ->getPossibleVocabulariesForCategory($this->getCategoryModel()->id);
        $control->addOption((new Option(0, '---')));

        // если форме известна характеристика.
        $current_vocabulary = $this->getVocabularyModel();
        if (null !== $current_vocabulary) {
            $possible_vocabularies = array_merge([$current_vocabulary], $possible_vocabularies);
            $control->setValue($current_vocabulary->id);
        }

        foreach ($possible_vocabularies as $vocabulary) {
            $vocabulary_type = (int)$vocabulary['type'] ?? 0;
            $option = (new Option($vocabulary['id'] ?? 0, $vocabulary['title'] ?? ''))
                ->addDataAttributes([
                    'vocabulary-type' => $vocabulary_type,
                    'is-type-select' => $vocabulary_type === Vocabulary::TYPE_SELECT ? "true" : "false",
                    'is-type-range' => $vocabulary_type === Vocabulary::TYPE_RANGE ? "true" : "false",
                ]);
            $control->addOption($option);
        }
        return $this;
    }

    /**
     * Метод заполняет контрол выбора единиц измерения значениями.
     * @param Selectize $control
     */
    private function populateUnitControl(Selectize $control)
    {
        $main_units = (new Unit())->getMineUnits();
        $units = ['' => ' --- Нет единицы измерения --- '];
        foreach ($main_units as $unit) {
            $units[$unit->code] = $unit->title . ' - (' . $unit->symbol_national . ')';
        }
        $control->setArrayOfOptions($units);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        if (!is_array($this->options)) {
            return [];
        }
        return $this->options;
    }

    /**
     * Кастомная валидация в зависимости от типа характеристики.
     * @return array
     */
    public function validate(): array
    {
        $this->loadControls();

        if ((int)$this->vocabulary_id === 0) {
            $this->addError('vocabulary_id', 'Выберите характеристику.');
        }
        $vocabulary = $this->getVocabularyModel();
        // валидация формы в зависимости от типа характеристики.
        if (null === $vocabulary) {
            $this->addError('vocabulary_id', 'Выбранной характеристики не существует.');
        }

        if (null === $this->getCategoryModel()) {
            $this->addError('vocabulary_id', 'Не пришла категория.');
        }

        if ($vocabulary->isSelectType()) {
            // если тип характеристики - селект, валидировать диапазон не нужно.
            $this->setAttributesNotValidate([
                $this->range_from_control->getName(),
                $this->range_to_control->getName(),
                $this->range_step_control->getName(),
            ]);
        } else {
            $this->setAttributesNotValidate([
                $this->vocabulary_options_control->getName(),
                $this->range_from_control->getName(),
                $this->range_to_control->getName(),
                $this->range_step_control->getName(),
            ]);
        }

        $this->populateModel();
        $this->getModel()->validate();
        $this->populateErrorsFromAR($this->getModel()->getErrors());

        return parent::validate();
    }

    protected function populateModel(): void
    {
        $form_data = $this->getFormData();
        /** @var CategoryVocabulary $model */
        $model = $this->getModel();
        foreach ($form_data as $property => $value) {
            if ($model->hasAttribute($property)) {
                $model->setAttribute($property, $value);
            }
        }
    }

    /**
     * @return CategoryVocabulary
     */
    public function getModel(): mixed
    {
        if (null === $this->model) {
            $model = CategoryVocabulary::find()
                ->where([
                    'category_id' => (int)$this->category_id,
                    'vocabulary_id' => (int)$this->vocabulary_id,
                ])
                ->one();
            if (null === $model) {
                $model = new CategoryVocabulary();
            }
            $this->model = $model;
        }
        return $this->model;
    }

    public function save(): bool
    {
        $vocabulary = $this->getVocabularyModel();

        $category_vocabulary_model = $this->getModel();
        $category_vocabulary_model->setAttributes([
            'category_id' => (int)$this->category_id,
            'vocabulary_id' => (int)$this->vocabulary_id,
        ]);
        // выбрали единицу измерения.
        if ((int)$this->unit_code > 0) {
            $category_vocabulary_model->unit_code = (int)$this->unit_code;
        }
        if ($vocabulary->isRangeType()) {
            $category_vocabulary_model->setRangeFrom((float)$this->range_from)
                ->setRangeTo((float)$this->range_to)
                ->setRangeStep((float)$this->range_step);
        }
        if ($vocabulary->isSelectType()) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $vocabulary->updateOptions((int)$this->category_id, $this->getOptions());
        }
        $result = $this->getModel()->save();
        if ($result) {
            CatalogProcessor::clearCache();
        }
        return $result;
    }

    public function getVocabularyModel(): ?Vocabulary
    {
        if (null === $this->vocabulary_model) {
            return $this->vocabulary_model = Vocabulary::findOne($this->vocabulary_id);
        }
        return $this->vocabulary_model;
    }

    public function setVocabularyId(int $vocabulary_id): self
    {
        $this->vocabulary_id = $vocabulary_id;
        return $this;
    }

    public function setCategoryId(int $category_id): self
    {
        $this->category_id = $category_id;
        return $this;
    }

    public function setForTableMode(bool $for_table_mode = true): self
    {
        $this->for_table_mode = $for_table_mode;
        return $this;
    }

    public function getCategoryModel(): ?Category
    {
        if (null === $this->category_model) {
            $this->category_model = Category::findOne($this->category_id);
            if (null !== $this->category_model) {
                $this->setTitle($this->category_model->title);
            }
            return $this->category_model;
        }
        return $this->category_model;
    }
}