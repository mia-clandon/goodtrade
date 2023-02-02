<?php

namespace backend\components\form\controls;

use yii\helpers\Json;

use common\assets\SelectizeAsset;
use common\assets\SelectizeDisableAsset;

use common\libs\traits\RegisterJsScript;

/**
 * Class Selectize
 * Php обёртка для плагина Selectize
 * @see https://selectize.github.io/selectize.js/
 * @package backend\components\bootstrap\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class Selectize extends Select {
    use RegisterJsScript;

    /** плагин: удаление тега по backspace. */
    const PLUGIN_RESTORE_ON_BACKSPACE = 'restore_on_backspace';
    /** плагин: удаление тега по крестику. */
    const PLUGIN_REMOVE_BUTTON = 'remove_button';

    const DEFAULT_DELIMITER = ',';

    protected $template_name = 'select';

    /** @var bool */
    private $can_create_options = false;

    /** @var array - список плагинов контрола. */
    private $plugins = [];

    /** @var array - свойства JS для инициализации плагина. */
    private $js_properties = [];

    /**
     * Установка плагина в список плагинов для Selectize.
     * @param string $plugin
     * @return $this
     */
    public function addPlugin($plugin) {
        $possible_plugins = $this->getPossiblePlugins();
        if (in_array($plugin, $possible_plugins)) {
            $this->plugins[] = $plugin;
        }
        return $this;
    }

    /**
     * Получение списка возможных плагинов.
     * @return array
     */
    private function getPossiblePlugins() {
        $possible_plugins = [];
        $reflected_class = new \ReflectionClass(__CLASS__);
        $constants = $reflected_class->getConstants();
        foreach ($constants as $constant_name => $constant_value) {
            if (strpos($constant_name, 'PLUGIN_') === 0) {
                $possible_plugins[$constant_name] = $constant_value;
            }
        }
        return $possible_plugins;
    }

    /**
     * Необходимые скрипты для работы selectize.
     */
    protected function registerFormAssets() {
        SelectizeAsset::register(\Yii::$app->getView());
        SelectizeDisableAsset::register(\Yii::$app->getView());
    }

    /**
     * Установка разделителей для тега.
     * @see https://selectize.github.io/selectize.js/  Tagging.
     * @param string $delimiter
     * @return $this
     */
    public function setDelimiter($delimiter = self::DEFAULT_DELIMITER) {
        $this->js_properties['delimiter'] = $delimiter;
        return $this;
    }

    /**
     * @see https://github.com/selectize/selectize.js/blob/master/docs/usage.md  Tagging, persist property.
     * @param bool $flag
     * @return $this
     */
    public function setPersist($flag) {
        $this->js_properties['persist'] = (bool)$flag;
        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function canCreateOptions($flag = true) {
        $this->can_create_options = (bool)$flag;
        return $this;
    }

    /**
     * Create property.
     * @see: https://github.com/selectize/selectize.js/blob/master/docs/usage.md
     * @param bool $flag
     * @return $this
     */
    public function setCreateProperty($flag = true) {
        $this->js_properties['create'] = $flag;
        return $this;
    }

    /**
     * If true, the "Add..." option is the default selection in the dropdown.
     * @param bool $flag
     * @return $this
     */
    public function addPrecedenceProperty($flag = true) {
        $this->js_properties['addPrecedence'] = $flag;
        return $this;
    }

    /**
     * The max number of items the user can select. 1 makes the control mono-selection, null allows an unlimited number of items.
     * @param integer $max_items
     * @return $this
     */
    public function setMaxItems($max_items) {
        $this->js_properties['maxItems'] = (int)$max_items;
        return $this;
    }

    /**
     * If true, Selectize will treat any options with a "" value like normal.
     * This defaults to false to accomodate the common <select> practice of having the first empty option to act as a placeholder.
     * @param bool $flag
     * @return $this
     */
    public function allowEmptyOption($flag = true) {
        $this->js_properties['allowEmptyOption'] = $flag;
        return $this;
    }

    /**
     * SortField property.
     * @see: https://selectize.github.io/selectize.js/
     * @param string $sort_field
     * @return $this
     */
    public function setSortField($sort_field) {
        $this->js_properties['sortField'] = (string)$sort_field;
        return $this;
    }

    /**
     * Устанавливает функцию create, для плагина selectize, "create" property.
     * @see https://selectize.github.io/selectize.js/ create property.
     * @param string $create_function
     * @return $this
     */
    public function setCreateFunction($create_function) {
        $this->js_properties['create'] = (string)$create_function;
        return $this;
    }

    /**
     * Возвращает функцию с-ва "create" по умолчанию.
     * @return string
     */
    public static function getDefaultCreateFunction() {
        return 'function(input) { return {
            value: input,
            text: input }
        }';
    }

    /**
     * Выполняется перед рендерингом контрола.
     */
    private function prepareAttributes() {
        $this->addClass('selectize-select-'. $this->getName());
        if ($this->can_create_options) {
            $this->addDataAttribute('can-create-options', '1');
        }
        $selectize_params = [];
        foreach ($this->js_properties as $property => $value) {
            $selectize_params[$property] = $value;
        }
        foreach ($this->plugins as $plugin) {
            $selectize_params['plugins'][] = $plugin;
        }
        $this->addDataAttribute('params', Json::encode($selectize_params));
    }

    public function registerScripts() {
        $this->registerJsScript();
    }

    /**
     * Регистрация скриптов
     * @return string
     */
    public function render(): string {

        $this->prepareAttributes();
        $this->registerJsScript();

        return parent::render();
    }
}