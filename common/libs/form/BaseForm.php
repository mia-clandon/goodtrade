<?php

namespace common\libs\form;

use common\libs\form\traits\AttributesTrait;

/**
 * Class BaseForm
 * Базовый класс формы.
 * @package common\libs\form
 * @author Артём Широких kowapssupport@gmail.com
 */
class BaseForm
{
    use AttributesTrait;

    # использование контролов окружения.
    public const MODE_USE_BACKEND_CONTROLS_TEMPLATE = 1;
    public const MODE_USE_FRONTEND_CONTROLS_TEMPLATE = 2;

    #region Типы форм
    public const ENCTYPE_APP_X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';
    public const ENCTYPE_MULTIPART_FORM_DATA = 'multipart/form-data';
    public const ENCTYPE_TEXT_PLAIN = 'text/plain';
    #endregion

    #region Типы запросов
    public const METHOD_POST = 'POST';
    public const METHOD_GET = 'GET';
    #endregion

    /** @var int|null */
    protected ?int $force_controls_template_env_mode = null;

    /** @var string|null Путь к шаблону */
    protected ?string $template_path = null;

    /** @var string Название формы. */
    private string $title = '';

    /** @var string - метод отправки формы. */
    private string $method = self::METHOD_POST;

    /** @var string - URL отправки формы. */
    private string $action = '';

    /** @var string|null - enctype */
    private ?string $enctype = null;

    /** @var string|null Название шаблона */
    private ?string $template = null;

    /** @var array Список property */
    private array $property_list = [];

    /** @var bool */
    private bool $ajax_mode = false;

    public function __set($name, $value)
    {
        $this->property_list[$name] = $value;
    }

    public function __get($name)
    {
        return (isset($this->property_list[$name])) ? $this->property_list[$name] : NULL;
    }

    //Устанавливает использование контролов с окружения.
    public function setControlsTemplateEnv(int $from): self
    {
        if (in_array($from, [
            self::MODE_USE_BACKEND_CONTROLS_TEMPLATE,
            self::MODE_USE_FRONTEND_CONTROLS_TEMPLATE
        ])) {
            $this->force_controls_template_env_mode = $from;
        }
        return $this;
    }

    //Разрешенные типы enctype.
    private function getAllowedEnctype(): array
    {
        return [
            self::ENCTYPE_APP_X_WWW_FORM_URLENCODED,
            self::ENCTYPE_MULTIPART_FORM_DATA,
            self::ENCTYPE_TEXT_PLAIN,
        ];
    }

    public function getId(): ?string
    {
        return $this->getAttribute('id');
    }

    public function setId(int|string $id): self
    {
        $this->addAttribute('id', $id);
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    //Установка метода отправки формы, по умолчанию METHOD_POST.
    public function setMethod(string $method): self
    {
        if (in_array($method, [self::METHOD_POST, self::METHOD_GET])) {
            $this->method = $method;
        }
        return $this;
    }

    public function setPostMethod(): self
    {
        $this->setMethod(self::METHOD_POST);
        return $this;
    }

    public function setGetMethod(): self
    {
        $this->setMethod(self::METHOD_GET);
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    protected function templateIsSet(): bool
    {
        return (!empty($this->template));
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setTemplateFileName(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    public function getTemplateFileName(): string
    {
        return $this->template;
    }

    public function getEnctype(): string
    {
        return $this->enctype;
    }

    public function setEnctype(string $enctype): self
    {
        if (in_array($enctype, $this->getAllowedEnctype())) {
            $this->addAttribute('enctype', $enctype);
            $this->enctype = $enctype;
        }
        return $this;
    }

    public function setEnctypeMultipartFormData(): self
    {
        $this->setEnctype(self::ENCTYPE_MULTIPART_FORM_DATA);
        return $this;
    }

    public function setAjaxMode(bool $flag = true): self
    {
        $this->ajax_mode = $flag;
        if ($flag) {
            $this->addDataAttribute('ajax-form', 'true');
        }
        return $this;
    }

    public function isAjaxMode(): bool
    {
        return $this->ajax_mode;
    }
}
