<?php

namespace common\libs\form\components;

use common\libs\form\Form;
use common\libs\form\traits\AttributesTrait;
use common\libs\form\traits\ValidatorTrait;
use common\libs\form\validators\client\BaseValidator as ClientValidator;
use common\libs\interfaces\IControl;
use common\libs\SmartyHelper;

/**
 * Class Control
 * @package common\libs\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Control implements IControl {
    use AttributesTrait;
    use ValidatorTrait;

    /** @var string - директория с view контрола. */
    protected $view_directory = 'components/form/views';

    /** @var string - идентификатор контрола формы.  */
    private $id = '';

    /** @var string - attribute name контрола формы. */
    private $name = '';

    /** @var bool - является ли массивом значений. */
    private $is_multiple = false;

    /** @var string - текст label (title)  */
    private $title = '';

    /** @var bool - имеет ли контрол клиенский валидатор */
    private $has_client_validator = false;

    /** @var int|null - использовать ли шаблоны установленного окружения.*/
    private $force_controls_template_env_mode;

    /** @var array - отдельные атрибуты клиенской валидации. */
    private $client_validator_attributes = [];

    /** @var null|string - название файла шаблона контрола. */
    protected $template_name = null;

    /** @var bool - скрыт ли контрол. */
    protected $is_hidden = false;

    /** @var String - значение контрола. */
    protected $value;

    /**
     * Метод выполняется перед рендерингом контрола.
     */
    protected function beforeRender() {}

    /**
     * Регистрирует скрипты контролов для вызова в \backend\components\form\Form::loadResources
     * для того чтобы ajax формы на странице регистрировали все свои ресурсы.
     */
    public function registerScripts() {}

    /**
     * Возвращает HTML код контрола.
     * @return string
     */
    public function render(): string {
        $this->beforeRender();
        return '';
    }

    /**
     * @param int $env_mode
     * @return $this
     */
    public function setControlTemplateEnv($env_mode) {
        if (in_array($env_mode, [Form::MODE_USE_BACKEND_CONTROLS_TEMPLATE, Form::MODE_USE_FRONTEND_CONTROLS_TEMPLATE])) {
            $this->force_controls_template_env_mode = $env_mode;
        }
        return $this;
    }

    /**
     * Метод проверяет, существует ли у контрола шаблон.
     * @return bool
     */
    public function hasControlTemplate() {
        $template_directory = $this->getTemplatePath();
        return file_exists($template_directory);
    }

    /**
     * Метод возвращает путь до файла с шаблоном контрола.
     * @return string
     */
    protected function getTemplatePath() {
        if (null === $this->template_name) {
            $class_name = explode('\\', get_class($this));
            $class_name = strtolower($class_name[count($class_name) - 1]);
            $this->template_name = $class_name;
        }
        $app_directory = \Yii::getAlias('@app');
        if (null !== $this->force_controls_template_env_mode) {
            $app_directory = $this->force_controls_template_env_mode === Form::MODE_USE_BACKEND_CONTROLS_TEMPLATE
                ? \Yii::getAlias('@backend')
                : \Yii::getAlias('@frontend');
        }
        return $app_directory. '/' .$this->view_directory. '/'. $this->template_name. '.tpl';
    }

    /**
     * Кастомное название файла шаблона.
     * @param $template_name
     * @return $this
     */
    public function setTemplateName($template_name) {
        $this->template_name = (string)$template_name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getTemplateName() {
        return $this->template_name;
    }

    /**
     * Рендерит шаблон контрола если таковой есть.
     * @param array $vars
     * @return string
     */
    public function renderTemplate(array $vars = []) {
        if (!$this->hasControlTemplate()) {
            return '';
        }
        $smarty = SmartyHelper::getInstance()->getSmartyObject(false);
        $not_use_cache = true;
        $base_vars = [
            'name'        => $this->getName(),
            'value'       => $this->getValue(),
            'label'       => $this->getTitle(),
            'control_id'  => $this->getId(),
        ];
        foreach ($base_vars as $name => $value) {
            $smarty->assign($name, $value, $not_use_cache);
        }
        foreach ($this->getAttributes() as $name => $value) {
            $smarty->assign($name, $value, $not_use_cache);
        }
        foreach ($vars as $name => $value) {
            $smarty->assign($name, $value, $not_use_cache);
        }
        return $smarty->fetch($this->getTemplatePath());
    }

    /**
     * Делает элемент скрытым.
     * @param bool $flag
     * @return $this
     */
    public function setAsHidden($flag = true) {
        $this->is_hidden = (bool)$flag;
        return $this;
    }

    /**
     * Навешивает клиенские валидаторы на контрол.
     * @param ClientValidator[] $validators
     * @return $this
     */
    public function setJsValidator(array $validators) {
        if (!empty($validators)) {
            $this->has_client_validator = true;
        }
        // все атрибуты валидаторов
        $all_attributes = [];
        foreach ($validators as $validator) {
            $all_attributes[] = $validator->getAttributeRules();
        }
        // data атрибуты
        $data_attributes = [];
        /**
         * Получаем все валидаторы навешанные на контрол
         * если валидаторов несколько то скомпановываю дата атрибуты для контрола
         * все data-validation через пробел, остальные через запятую.
         */
        foreach ($all_attributes as $validators) {
            foreach ($validators as $attribute_name => $value) {
                if (array_key_exists($attribute_name, $data_attributes)) {
                    $comma = ',';
                    if ($attribute_name=='data-validation') {
                        $comma = ' ';
                    }
                    $data_attributes[$attribute_name] .= $comma. $value;
                }
                else {
                    $data_attributes[$attribute_name] = $value;
                }
            }
        }
        $this->client_validator_attributes = $data_attributes;
        $this->addAttributes($data_attributes);
        return $this;
    }

    /**
     * @return array
     */
    public function getClientValidatorAttributes(): array {
        return $this->client_validator_attributes;
    }

    /**
     * Имеет ли контрол валидацию на клиенте.
     * @return bool
     */
    public function hasClientValidator() {
        return (bool)$this->has_client_validator;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name) {
        $this->name = (string)$name;
        return $this;
    }

    /**
     * Name контрола
     * @return string
     */
    public function getName(): string {
        return (string)$this->name;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id) {
        $this->id = (string)$id;
        $this->addAttribute('id', $id);
        return $this;
    }

    /**
     * Id контрола
     * @return string
     */
    public function getId() {
        return (string)$this->id;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title) {
        $this->title = (string)$title;
        return $this;
    }

    /**
     * Label текст контрола
     * @return string
     */
    public function getTitle(): string {
        return (string)$this->title;
    }

    /**
     * @param string|array $value
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string|array
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @return $this
     */
    public function setDisabled() {
        $this->addAttribute('disabled', 'disabled');
        return $this;
    }

    /**
     * @return $this
     */
    public function setEnabled() {
        $this->removeAttribute('disabled');
        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setIsMultiple($flag = true) {
        $this->is_multiple = (bool)$flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMultiple() {
        return (bool)$this->is_multiple;
    }
}