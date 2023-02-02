<?php

namespace common\libs\form\components;

use yii\helpers\Html;

/**
 * Кнопка
 * Class Button
 * @package common\libs\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class Button extends Control {

    /** Тип отправка */
    const TYPE_SUBMIT = 'submit';
    /** Тип сброс */
    const TYPE_RESET = 'reset';
    /** Тип кнопка */
    const TYPE_BUTTON = 'button';

    /** @var array Возможные типы button'a */
    public static $types = [
        self::TYPE_SUBMIT,
        self::TYPE_RESET,
        self::TYPE_BUTTON,
    ];

    /** @var string */
    private $type = self::TYPE_BUTTON;

    /** @var string Контент на кнопке */
    private $content = 'Button !';

    /**
     * @return string
     */
    public function getContent() {
        return (string)$this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * Установка типа button.
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self {
        if (\in_array($type, self::$types)) {
            $this->type = $type;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return string
     */
    public function render(): string {
        parent::render();
        $this->addAttribute('type', $this->getType());
        $this->addAttribute('name', $this->getName());
        return Html::button($this->getContent(), $this->getAttributes());
    }
}