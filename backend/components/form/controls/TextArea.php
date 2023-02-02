<?php

namespace backend\components\form\controls;

use common\assets\CKEditorAsset;
use common\assets\TinyMCEAsset;
use common\libs\form\components\TextArea as BaseTextArea;
use common\libs\traits\RegisterJsScript;

/**
 * Class TextArea
 * @package backend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class TextArea extends BaseTextArea {
    use RegisterJsScript;

    /** @var bool - загрузка TinyMCE редактора. */
    private $load_tinymce_editor = false;

    /** @var bool - загрузка CKEditor редактора. */
    private $load_ckeditor = false;

    /**
     * @param bool $flag
     * @return $this
     */
    public function setLoadTinyMCE($flag = true) {
        $this->load_tinymce_editor = (bool)$flag;
        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setLoadCKEditor($flag = true) {
        $this->load_ckeditor = true;
        return $this;
    }

    /**
     * @param int $rows_count
     * @return $this
     */
    public function setRowsCount($rows_count) {
        $this->addAttribute('rows', (int)$rows_count);
        return $this;
    }

    /**
     * @param int $cols_count
     * @return $this
     */
    public function setColsCount($cols_count) {
        $this->addAttribute('cols', (int)$cols_count);
        return $this;
    }

    protected function registerFormAssets() {
        if ($this->load_tinymce_editor) {
            // плагин TinyMCE
            TinyMCEAsset::register(\Yii::$app->getView());
        }
        if ($this->load_ckeditor) {
            // плагин  CKEditor
            CKEditorAsset::register(\Yii::$app->getView());
        }
    }

    protected function beforeRender() {
        parent::beforeRender();
        $this->addClass('form-control');

        if ($this->load_tinymce_editor) {
            $this->addClass('tinymce-control');
            $this->registerJsScript();
        }
        if ($this->load_ckeditor) {
            $this->addClass('ckeditor-control');
            $this->registerJsScript();
        }
    }

    /**
     * Рендеринг компонента
     * @return string
     */
    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control' => $control,
            'errors' => $this->getErrors(),
            'errors_string' => $this->getErrorsAsString(','),
        ]);
    }
}