<?php

namespace common\libs\form\traits;

/**
 * Class AttributesTrait
 * @package common\libs\form\traits
 * @author Артём Широких kowapssupport@gmail.com
 */
trait AttributesTrait {

    /** @var array  */
    protected $attributes = [];

    /** @var array  */
    protected $classes = [];

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes) {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function addAttributes(array $attributes) {
        foreach ($attributes as $attribute_name => $attribute) {
            $this->addAttribute($attribute_name, $attribute);
        }
        return $this;
    }

    /**
     * Добавление нового класса
     * @param string $class_name
     * @return $this
     */
    public function addClass($class_name) {
        // с ключем class_name, дабы исключить повторных классов.
        $this->classes[$class_name] = $class_name;
        $this->addAttribute('class', $this->getClassString());
        return $this;
    }

    /**
     * @param array $classes
     * @return $this
     */
    public function addClasses(array $classes) {
        foreach ($classes as $class) {
            $this->addClass($class);
        }
        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function setClass($class) {
        $this->classes = [];
        $this->addClass($class);
        return $this;
    }

    /**
     * @param array $classes
     * @return $this
     */
    public function setClasses(array $classes) {
        $this->classes = [];
        $this->addClasses($classes);
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addAttribute($name, $value) {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getAttribute($name) {
        return (isset($this->attributes[$name])) ?
            $this->attributes[$name] : null;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addDataAttribute($name, $value) {
        $this->addAttribute('data-'.$name, $value);
        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function addDataAttributes(array $attributes) {
        foreach ($attributes as $attribute => $value) {
            $this->addDataAttribute($attribute, $value);
        }
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function removeAttribute($name) {
        if (isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
        }
        return $this;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getDataAttribute($name) {
        return (isset($this->attributes['data-'.$name])) ?
            $this->attributes['data-'.$name] : null;
    }

    /**
     * Возвращает все атрибуты
     * @return array
     */
    public function getAttributes(): array {
        return $this->attributes;
    }

    /**
     * Возвращает строковое представление классов.
     * @return string
     */
    public function getClassString(): string {
        return implode(' ', $this->classes);
    }

    /**
     * @return string
     */
    public function getAttributesString(): string {
        $result = [];
        foreach ($this->getAttributes() as $attribute_name => $attribute_value) {
            $result[] = $attribute_name.'="'.$attribute_value.'"';
        }
        return implode(' ', $result);
    }

    /**
     * @param string $placeholder
     * @return static
     */
    public function setPlaceholder($placeholder) {
        if (!empty($placeholder)) {
            $this->addAttribute('placeholder', $placeholder);
        }
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlaceholder() {
        if (isset($this->attributes['placeholder'])) {
            return $this->attributes['placeholder'];
        }
        return null;
    }
}