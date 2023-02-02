<?php

namespace common\libs\form\components;

use yii\base\Exception;
use yii\helpers\Html;

/**
 * Input
 * Class Input
 * @package common\libs\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class Input extends Control {

    /** текстовый input */
    const TYPE_TEXT = 'text';
    /** скрытый input */
    const TYPE_HIDDEN = 'hidden';
    /** пароль */
    const TYPE_PASSWORD = 'password';
    /** число */
    const TYPE_NUMBER = 'number';
    /** файл */
    const TYPE_FILE = 'file';
    /** чекбокс */
    const TYPE_CHECKBOX = 'checkbox';
    /** телефон */
    const TYPE_PHONE = 'tel';
    /** submit */
    const TYPE_SUBMIT = 'submit';
    /** radio */
    const TYPE_RADIO = 'radio';

    /** @var array Возможные типы инпута */
    public static $types = [
        self::TYPE_TEXT,
        self::TYPE_HIDDEN,
        self::TYPE_PASSWORD,
        self::TYPE_NUMBER,
        self::TYPE_FILE,
        self::TYPE_SUBMIT,
        self::TYPE_PHONE,
        self::TYPE_RADIO,
    ];

    /** @var string */
    private $type = self::TYPE_TEXT;

    /**
     * Установка типа input
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self {
        if (in_array($type, self::$types)) {
            $this->type = $type;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isHiddenInput() {
        return $this->getType() == self::TYPE_HIDDEN;
    }

    /**
     * Прячет контрол (display: none.)
     * @return $this
     */
    public function setDisplayNone() {
        $this->addAttribute('style', 'display: none;');
        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render(): string {
        parent::render();
        if (is_array($this->value)) {
            throw new Exception('Value is not correct !');
        }
        return Html::input(
            $this->type,
            $this->isMultiple() ? $this->getName().'[]' : $this->getName(),
            $this->value,
            $this->getAttributes()
        );
    }
}