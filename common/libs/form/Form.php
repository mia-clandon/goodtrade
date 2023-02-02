<?php /** @noinspection DuplicatedCode */

namespace common\libs\form;

use common\assets\FormAsset;
use common\libs\Env;
use common\libs\form\assets\ClientValidator;
use common\libs\form\components\Control;
use common\libs\traits\RegisterJsScript;
use yii\base\{Controller, Exception, Model};
use yii\db\ActiveRecord;
use yii\helpers\{ArrayHelper, Html};
use yii\web\{Response, View};

/**
 * Class Form
 * @package common\libs\form
 * @author Артём Широких kowapssupport@gmail.com
 */
class Form extends BaseForm
{
    use RegisterJsScript;

    /** Название папки содержащей файл шаблона */
    const TEMPLATE_FOLDER_NAME = 'form';
    /** Расширение шаблона формы */
    const TEMPLATE_EXTENSION = 'tpl';
    /** Разделитель ошибок */
    const ERROR_COMMA = '<br>';

    protected bool $controls_was_loaded = false;

    /** @var array Данные с POST/GET */
    private array $form_data = [];

    /** @var array Хранение пользовательских ошибок */
    private array $form_errors = [];

    /** @var array Хранение ошибок контролов форм */
    private array $control_errors = [];

    /** @var null|Model|ActiveRecord */
    protected mixed $model = null;

    /** @var array - Массив с name контролов которые нужно валидировать. */
    private array $attribute_names_for_validate = [];

    /** @var array - Массив с name контролов которые не нужно валидировать. */
    private array $attribute_names_not_validate = [];

    /** @var array - Зарегистрированные переменные шаблона. */
    private array $registered_template_vars = [];

    /** @var Control[] */
    private array $controls = [];

    /** @var bool - рендерить ли <form></form> теги. */
    private bool $render_form_tags = true;

    /**
     * Использовать новую вёрстку форм.
     * todo: удалить костыльный метод после переноса всех форм и тд на новую вёрстку.
     * @return $this
     */
    public function setUseNewCodingMode(): self
    {
        $this->addDataAttribute('b2b', 'true');
        return $this;
    }

    /**
     * @param Control $control
     * @return $this
     * @throws Exception
     */
    public function addControl(Control $control): self
    {
        $control_name = $control->getName();
        if (empty($control_name)) {
            throw new Exception('Control ' . get_class($control) . ' must contain the name.');
        }
        if (isset($this->controls[$control->getName()])) {
            throw new Exception('Control with the same name "' . $control->getName() . '" already exists.');
        }
        if (null !== $this->force_controls_template_env_mode) {
            $control->setControlTemplateEnv($this->force_controls_template_env_mode);
        }
        $this->controls[$control->getName()] = $control;
        return $this;
    }

    /**
     * @param Control $control
     * @return $this
     * @throws Exception
     */
    public function updateControl(Control $control): self
    {
        if (!isset($this->controls[$control->getName()])) {
            throw new Exception('Control "' . $control->getName() . '" not registered.');
        }
        $this->controls[$control->getName()] = $control;
        return $this;
    }

    /**
     * Инициализация контролов формы, регистрировать все контролы необходимо там.
     */
    protected function initControls(): void
    {
    }

    // Вызывает инициализацию контролов с дочерних форм и заполняет контролы данными.
    protected function loadControls(): self
    {
        if (!$this->controls_was_loaded) {
            $this->controls_was_loaded = true;
            $this->initControls();
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->initControlValues();
            $this->registerFormAssets();
        }
        return $this;
    }

    /**
     * @return components\Control[]
     */
    public function getControls(): array
    {
        $this->loadControls();
        return $this->controls;
    }

    /**
     * Добавляет переменные для шаблона формы.
     * @param array $vars
     * @return $this
     */
    public function addTemplateVars(array $vars): self
    {
        foreach ($vars as $var_name => $var_value) {
            $this->registered_template_vars[$var_name] = $var_value;
        }
        return $this;
    }

    /**
     * Устанавливает переменные для шаблона формы.
     * @param array $vars
     * @return $this
     */
    public function setTemplateVars(array $vars): self
    {
        $this->registered_template_vars = $vars;
        return $this;
    }

    /**
     * Устанавливает массив с названиями контролов для валидации.
     * @param array $attributes
     * @return $this
     */
    public function setAttributesForValidate(array $attributes): self
    {
        $this->attribute_names_for_validate = $attributes;
        return $this;
    }

    /**
     * Устанавливает массив с названиями контролов валидацию которых - игнорировать.
     * @param array $attributes
     * @return $this
     */
    public function setAttributesNotValidate(array $attributes): self
    {
        $this->attribute_names_not_validate = array_merge($this->attribute_names_not_validate, $attributes);
        return $this;
    }

    /**
     * Валидация формы.
     * Перебирает все контролы формы и вызывает их validate
     * @return array
     */
    public function validate(): array
    {
        $attributes_for_validate = $this->attribute_names_for_validate;
        $attributes_for_skip_validate = $this->attribute_names_not_validate;

        foreach ($this->getControls() as $control) {
            $run_validation = false;
            // атрибуты для которых запускать валидацию.
            if (empty($attributes_for_validate) || in_array($control->getName(), $attributes_for_validate)) {
                // атрибуты для которых не запускать валидацию.
                if (empty($attributes_for_skip_validate) || !in_array($control->getName(), $attributes_for_skip_validate)) {
                    $run_validation = true;
                }
            }
            if ($run_validation) {
                if ($errors = $control->validate()) {
                    $this->control_errors[$control->getName()] = $errors;
                }
            }
        }
        return ArrayHelper::merge($this->form_errors, $this->control_errors);
    }

    public function isValid(): bool
    {
        return (empty($this->getErrors()));
    }

    public function setFormData(array $data): static
    {
        //todo: проверка на атрибуты формы (дабы ничего лишнего в форму не установить).
        foreach ($data as $property_name => $value) {
            // в случае если пришел массив значений.
            if (is_array($value)) {
                $array_values = [];
                foreach ($value as $additional_property => $additional_value) {
                    $array_values[$property_name . '[' . $additional_property . ']']
                        = $additional_value;
                }
                $this->setFormData($array_values);
            }
            $control = $this->getControlByName($property_name);
            if ($control !== null) {
                $this->{$property_name} = $value;
                $this->form_data[$property_name] = $value;
                $control->setValue($value);
            }
        }
        return $this;
    }

    public function getFormData(): array
    {
        return $this->form_data;
    }

    public function getRequestData(): array
    {
        if ($this->getMethod() === self::METHOD_GET) {
            return \Yii::$app->request->get();
        }
        return \Yii::$app->request->post();
    }

    //Данные всех контролов.
    public function getControlsData(): ?array
    {
        $data = [];
        foreach ($this->getControls() as $control) {
            $data[$control->getName()] = $control->getValue();
        }
        return array_merge($this->getFormData(), $data);
    }

    public function getControlData(string $property): mixed
    {
        $controls_data = $this->getControlsData();
        /** @noinspection PhpUnhandledExceptionInspection */
        return ArrayHelper::getValue($controls_data, $property);
    }

    /**
     * Заполняет форму ошибками с ActiveRecord модели.
     * @param array $errors_list
     * @return $this
     */
    protected function populateErrorsFromAR(array $errors_list): self
    {
        foreach ($errors_list as $attribute => $errors) {
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    // может быть массив в массиве ошибок.
                    if (is_array($error)) {
                        foreach ($error as $message) {
                            $this->addError($attribute, $message);
                        }
                    } else {
                        $this->addError($attribute, $error);
                    }
                }
            } else {
                $this->addError($attribute, $errors);
            }
        }
        return $this;
    }

    //Регистрация нового контрола формы.
    protected function registerControl(Control $control): self
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->addControl($control);
        return $this;
    }

    //Метод добавляет к пути /{название папки шаблонов формы}/{название шаблона}.{расширение}
    private function addTemplateName(string $path): string
    {
        $path .= '/' .
            self::TEMPLATE_FOLDER_NAME .
            '/' .
            $this->getTemplateFileName() .
            '.' .
            self::TEMPLATE_EXTENSION;
        return $path;
    }

    /**
     * Получение пути к view файла формы.
     * @throws \Exception
     */
    private function getTemplatePath(): string
    {
        if (!empty($this->template_path)) {
            $path = $this->addTemplateName($this->template_path);
        } else {
            $path = $this->getTemplatePathByController();
        }
        if (!file_exists($path)) {
            $error_message = 'View файл формы отсутствует, {path}';
            $error_message = str_replace('{path}', $path, $error_message);
            \Yii::error($error_message);
            throw new \Exception($error_message);
        }
        return $path;
    }

    public function setTemplatePath(string $template_path): self
    {
        $this->template_path = \Yii::getAlias($template_path);
        return $this;
    }

    /**
     * Метод возвращает путь ко view файлу формы относительно текущего контроллера.
     * @return string
     * @throws \Exception
     */
    private function getTemplatePathByController(): string
    {
        /**
         * @var $controller Controller|null
         */
        $controller = \Yii::$app->controller;
        if ($controller === null) {
            throw new \Exception('Не известный контроллер.');
        }
        return $this->addTemplateName($controller->getViewPath());
    }

    /**
     * @throws \Exception
     */
    private function getSmartyObject(): \Smarty
    {
        $smarty = new \Smarty();
        $smarty_config = ArrayHelper::getValue(\Yii::$app->params, 'form.template', false);
        $caching = $smarty_config ? ArrayHelper::getValue($smarty_config, 'caching', true) : true;
        $smarty->caching = $caching;

        if ($caching) {
            $cache_lifetime = ArrayHelper::getValue($smarty_config, 'cache_time', false);
            if ($cache_lifetime) {
                $smarty->setCacheLifetime($cache_lifetime);
            }
            $smarty->setCacheDir(\Yii::getAlias('@runtime/forms/cache'));
            $smarty->setCompileDir(\Yii::getAlias('@runtime/forms/compile'));
        }

        $debugging = ArrayHelper::getValue($smarty_config, 'debugging', false);
        if ($debugging) {
            $smarty->debugging = $debugging;
        }
        return $smarty;
    }

    /**
     * Рендеринг файла формы.
     * @return string
     * @throws \Exception
     */
    private function renderTemplateForm(): string
    {
        $template_path = $this->getTemplatePath();
        /**
         * За рендеринг шаблона формы отвечает Smarty.
         * @var $smarty \Smarty
         * @var $control Control
         */
        $smarty = $this->getSmartyObject();

        $not_use_cache = true;

        # Теги формы
        $smarty->assign('form_title', $this->getTitle(), $not_use_cache);
        $smarty->assign('form_start', $this->getFormStart(), $not_use_cache);
        $smarty->assign('form_end', $this->getFormEnd(), $not_use_cache);

        $controls = $this->getControls();

        # Регистрация контролов в шаблон
        foreach ($controls as $control) {
            // регистрация HTML контрола
            $smarty->assign($control->getName(), $this->renderControl($control), $not_use_cache);
            // регистрация ошибок контрола
            $smarty->assign($control->getName() . '_errors', $this->getErrors($control->getName()), $not_use_cache);
            // регистрация title
            $smarty->assign($control->getName() . '_label', $control->getTitle(), $not_use_cache);
            // ошибки через разделитель
            $smarty->assign($control->getName() . '_error_string', implode(self::ERROR_COMMA, $this->getErrors($control->getName())), $not_use_cache);
        }
        // регистрация всех ошибок в шаблон
        $smarty->assign('all_errors', $this->getErrors());

        // переменные шаблона
        foreach ($this->registered_template_vars as $var_name => $var_value) {
            $smarty->assign($var_name, $var_value, true);
        }

        return $smarty->fetch($template_path);
    }

    //Нужно ли рендерить <form></form> тэги.
    public function needRenderFormTags(bool $flag = true): self
    {
        $this->render_form_tags = $flag;
        return $this;
    }

    //Рендеринг чистой формы, возвращает HTML всех контролов.
    private function renderClearForm(): string
    {
        /**
         * TODO: cache
         * @var $control Control
         */
        $controls = [];
        foreach ($this->getControls() as $control) {
            $controls[] = $control->render();
        }
        $output = '';
        if ($this->render_form_tags) {
            $output .= $this->getFormStart();
        }
        $output .= implode('', $controls);
        if ($this->render_form_tags) {
            $output .= $this->getFormEnd();
        }
        return $output;
    }

    /**
     * Рендеринг контрола, если у него есть декоратор,
     * то рендерится контрол через декоратор.
     * @param Control $control
     * @return string
     */
    private function renderControl(Control $control): string
    {
        return $control->render();
    }

    private function getFormStart(): string
    {
        return Html::beginForm(
            $this->getAction(), $this->getMethod(), $this->getAttributes()
        );
    }

    private function getFormEnd(): string
    {
        return Html::endForm();
    }

    //Скрипты форм.
    protected function registerFormAssets()
    {
        if (Env::i()->isBackendApp() && $this->isAjaxMode()) {
            FormAsset::register(\Yii::$app->getView());
        }
    }

    /**
     * Возвращает HTML код формы.
     * @return string
     * @throws
     */
    public function render(): string
    {
        // клиентская валидация.
        $this->connectValidatorJs();

        if ($this->templateIsSet()) {
            return $this->renderTemplateForm();
        }
        return $this->renderClearForm();
    }

    /**
     * Подключение js'валидации
     * Внимание !!! Только на Backend окружении!!!
     * @return $this
     */
    protected function connectValidatorJs(): self
    {
        // проверяю, если контролы имеют валидатор, то подключаю клиенскую валидацию
        if ($this->controlsHasClientValidator() && Env::i()->isBackendApp()) {
            /** @var Controller $controller */
            $controller = \Yii::$app->controller;
            ClientValidator::register($controller->getView());
            \Yii::$app->view->registerJs("$.validate({lang: 'ru'});", View::POS_READY);
        }
        return $this;
    }

    //Имеется ли у контролов формы валидатор.
    private function controlsHasClientValidator(): bool
    {
        foreach ($this->getControls() as $control) {
            if ($control->hasClientValidator()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Установка значений контролам.
     * @throws \Exception
     */
    protected function initControlValues(): self
    {
        //Установка значений с POST/GET либо получение значений с модели. (приоритетнее).
        foreach ($this->getControls() as $control) {

            $control_name = $control->getName();

            if (null !== $this->model && !$control->getTitle()) {
                // label для контрола можно получить из модели.
                $label = method_exists($this->model, 'attributeLabels') ?
                    ArrayHelper::getValue($this->model->attributeLabels(), $control_name) : null;
                if (method_exists($control, 'setTitle') && $label) {
                    $control->setTitle($label);
                }
            }

            // если установлена модель - значение с модели
            if (
                $this->model instanceof ActiveRecord
                && !$this->model->isNewRecord
                && !\Yii::$app->request->isPost
                && $this->model->hasAttribute($control->getName())
            ) {
                if (null !== $control_name && $control->getValue() == null) {
                    $value = $this->model->getAttribute($control->getName());
                    $control->setValue($value);
                }
            } else {
                // установка значений с данных формы
                if ($value = ArrayHelper::getValue($this->form_data, $control->getName())) {
                    $control->setValue($value);
                }
            }
        }
        return $this;
    }

    /**
     * Для заполнения модели AR из формы.
     */
    protected function populateModel(): void
    {
    }

    //Метод сохраняет данные формы в модели.
    public function save(): bool
    {
        return false;
    }

    public function loadResources(): static
    {
        return $this;
    }

    /**
     * Метод валидирует форму, и вызывает метод save формы только при AJAX запросе.
     * Если запрос не ajax, метод возвращает пустой массив.
     * @param array $response_data - дополнительные данные для ответа.
     * @return array
     */
    public function ajaxValidateAndSave(array $response_data = []): array
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $this->setFormData(\Yii::$app->request->post());
            $this->validate();
            if ($this->isValid() && $this->save()) {
                return ['success' => true] + $response_data;
            } else {
                return ['errors' => $this->getErrors()];
            }
        }
        return [];
    }

    //Очистка формы.
    public function clear(): self
    {
        foreach ($this->getControls() as $control) {
            $control->setValue('');
        }
        return $this;
    }

    public function addError(string $control_name, string $message): self
    {
        $control = $this->getControlByName($control_name);
        if ($control) {
            $control->addError($message);
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->updateControl($control);
            $this->control_errors[$control_name][] = $message;
        } else {
            $this->form_errors[$control_name][] = $message;
        }
        return $this;
    }

    public function getFormErrors(?string $control_name = null): array
    {
        if (!is_null($control_name)) {
            return (isset($this->form_errors[$control_name]))
                ? $this->form_errors[$control_name]
                : [];
        }
        return $this->form_errors;
    }

    public function getControlErrors(?string $control_name = null): array
    {
        if (!is_null($control_name)) {
            return (isset($this->control_errors[$control_name])) ? $this->control_errors[$control_name] : [];
        }
        return $this->control_errors;
    }

    public function getErrors(?string $control_name = null): array
    {
        $errors = ArrayHelper::merge($this->control_errors, $this->form_errors);
        if (!is_null($control_name)) {
            return (isset($errors[$control_name])) ? $errors[$control_name] : [];
        }
        return $errors;
    }

    public function getFirstError(): ?string
    {
        $errors = $this->getErrors();
        if (!empty($errors)) {
            foreach ($errors as $control => $error) {
                /** @noinspection PhpUnhandledExceptionInspection */
                return ArrayHelper::getValue($error, 0, '');
            }
        }
        return null;
    }

    private function getControlByName(string $control_name): ?Control
    {
        $controls = $this->getControls();
        if (isset($controls[$control_name])) {
            return $controls[$control_name];
        }
        return null;
    }

    private function getControlNames(): array
    {
        $control_names = [];
        foreach ($this->getControls() as $control) {
            $control_names[] = $control->getName();
        }
        return $control_names;
    }

    public function getModel(): mixed
    {
        return $this->model;
    }

    public function setModel(mixed $model): static
    {
        $this->model = $model;
        return $this;
    }
}