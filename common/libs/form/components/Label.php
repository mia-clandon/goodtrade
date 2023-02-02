<?php

namespace common\libs\form\components;

use yii\helpers\Html;

/**
 * Label
 * @package common\libs\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class Label extends Control {

    /** @var string|null Контент label'a */
    private $content = null;
    /** @var string|null for attribute */
    private $for = null;

    /**
     * @return null|string
     */
    public function getFor() {
        return $this->for;
    }

    /**
     * @param null|string $for
     * @return Label
     */
    public function setFor($for) {
        if (!empty($for)) {
            $this->for = $for;
        }
        return $this;
    }

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
     * @return string
     */
    public function render(): string {
        parent::render();
        return Html::label($this->getContent(), $this->getFor(), $this->getAttributes());
    }
}