<?php

namespace backend\components\form\controls;

use common\libs\form\components\Button as BaseButton;

/**
 * Class Button
 * @package backend\components\bootstrap\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class Button extends BaseButton {

    /** Типы кнопок */
    const BTN_TYPE_DEFAULT  = 'btn-default';
    const BTN_TYPE_PRIMARY  = 'btn-primary';
    const BTN_TYPE_SUCCESS  = 'btn-success';
    const BTN_TYPE_INFO     = 'btn-info';
    const BTN_TYPE_WARNING  = 'btn-warning';
    const BTN_TYPE_DANGER   = 'btn-danger';
    const BTN_TYPE_LINK     = 'btn-link';

    /** Размеры кнопок */
    const BTN_SIZE_LARGE    = 'btn-lg';
    const BTN_SIZE_SMALL    = 'btn-sm';
    const BTN_SIZE_XS       = 'btn-xs';

    /** @var array */
    public static $types = [
        self::BTN_TYPE_DEFAULT,
        self::BTN_TYPE_PRIMARY,
        self::BTN_TYPE_SUCCESS,
        self::BTN_TYPE_INFO,
        self::BTN_TYPE_WARNING,
        self::BTN_TYPE_DANGER,
        self::BTN_TYPE_LINK,
    ];

    /** @var array */
    public static $sizes = [
        self::BTN_SIZE_LARGE,
        self::BTN_SIZE_SMALL,
        self::BTN_SIZE_XS,
    ];

    /** @var string */
    private $button_type = self::BTN_TYPE_DEFAULT;
    /** @var string */
    private $button_size = '';
    /** @var null|string */
    private $icon = null;

    /**
     * @param string $type
     * @return $this
     */
    public function setButtonType($type) {
        if (!empty($type) && in_array($type, self::$types)) {
            $this->button_type = $type;
            $this->addClass($type);
        }
        return $this;
    }

    /**
     * @param string $size
     * @return $this
     */
    public function setButtonSize($size) {
        if (!empty($size) && in_array($size, self::$sizes)) {
            $this->button_size = $size;
            $this->addClass($size);
        }
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return static
     */
    public function setIcon($icon) {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @param string $url
     * @return static
     */
    public function setRedirectOnClick($url) {
        $this->addAttribute('onclick', 'document.location.href="'.$url."\"");
        return $this;
    }

    public function render(): string {
        $this->addClass('btn');
        if (!is_null($this->getIcon())) {
            $icon = $this->getIcon();
            $this->setContent( Icon::createIconByClass($icon)->render().' '.$this->getContent() );
        }
        return parent::render();
    }
}