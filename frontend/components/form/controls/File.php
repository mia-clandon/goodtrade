<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Control;
use common\libs\form\components\Input;

/**
 * Контрол для загрузки файлов.
 * Class File
 * todo: нужно переписать js класс для этого контрола.
 * todo: продумать все возможные валидации и тд.
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class File extends Control {

    const FILE_INPUT_NAME_POSTFIX = '_file_input';

    /** @var Input */
    private $file_control;

    /** @var array */
    private $file_formats = [];

    /** @var int */
    private $file_count = 1;

    /** @var string */
    private $upload_url = '/api/uploader/upload-file';

    /** @var int */
    private $max_file_size = 20000000;

    public function beforeRender() {
        parent::beforeRender();
    }

    /**
     * @param string $format
     * @return $this
     */
    public function addAllowedFileFormat(string $format) {
        $this->file_formats[] = $format;
        return $this;
    }

    /**
     * @param array $formats
     * @return $this
     */
    public function addAllowedFileFormats(array $formats) {
        foreach ($formats as $format) {
            $this->addAllowedFileFormat($format);
        }
        return $this;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setMaxFileCount(int $count) {
        $this->file_count = $count;
        return $this;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setMaxFileSize(int $size) {
        $this->max_file_size = (int)$size * 1024 * 1024;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUploaderUrl(string $url) {
        $this->upload_url = (string)$url;
        return $this;
    }

    /**
     * @return Input
     */
    private function getFileControl(): Input {
        if (is_null($this->file_control)) {
            $this->file_control = (new Input())
                ->setType(Input::TYPE_FILE)
                ->addClass('input-file-field')
                ->setIsMultiple($this->isMultiple())
                ->setName($this->getName());
        }
        return $this->file_control;
    }

    /**
     * Возвращает список параметров с настройками для загрузчика.
     * @return array
     */
    private function getUploaderOptions(): array {
        return [
            'max-number-of-files' => $this->file_count,
            'accept-file-types' => implode('|', $this->file_formats),
            'max-file-size' => $this->max_file_size,
            'url' => $this->upload_url,
        ];
    }

    public function render(): string {
        parent::render();
        return $this->renderTemplate([
            'file_control' => $this->getFileControl()->render(),
            'options' => $this->getUploaderOptions(),
        ]);
    }
}