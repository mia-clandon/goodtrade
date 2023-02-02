<?php

namespace backend\components\qq;

use yii\helpers\ArrayHelper;

/**
 * Class FileUploader
 * @package backend\components\form
 * @author Артём Широких kowapssupport@gmail.com
 */
class FileUploader {

    private $allowed_extensions = [];
    private $size_limit = 10485760;
    /** @var UploadedFileXhr */
    private $file;

    /**
     * FileUploader constructor.
     * @param array $allowed_extensions
     * @param int $size_limit
     */
    function __construct(array $allowed_extensions = [], $size_limit = 10485760) {
        $allowed_extensions = array_map("strtolower", $allowed_extensions);

        $this->allowed_extensions = $allowed_extensions;
        $this->size_limit = $size_limit;

        $this->checkServerSettings();

        $file = ArrayHelper::getValue($_GET, 'file',
            ArrayHelper::getValue($_POST, 'file')
        );
        if ($file) {
            $this->file = new UploadedFileXhr();
        }
        else {
            $this->file = false;
        }
    }

    /**
     * @return null|string
     */
    public function getName() {
        if ($this->file) {
            return $this->file->getName();
        }
        return '';
    }

    /**
     * Проверяет настройки сервера.
     */
    private function checkServerSettings() {
        $post_size = $this->toBytes(ini_get('post_max_size'));
        $upload_size = $this->toBytes(ini_get('upload_max_filesize'));

        if ($post_size < $this->size_limit || $upload_size < $this->size_limit){
            $size = max(1, $this->size_limit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    /**
     * @param $str
     * @return int|string
     */
    private function toBytes($str) {
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns ['success' => true, 'newFilename' => 'myDoc123.doc'[ or ['error' => 'error message']
     * @param string $upload_directory
     * @param bool $replace_old_file
     * @return array
     */
    function handleUpload($upload_directory, $replace_old_file = FALSE) {

        if (!is_writable($upload_directory)){
            return ['error' => "Server error. Upload directory isn't writable."];
        }

        if (!$this->file){
            return ['error' => 'Выберите файл для загрузки.'];
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return ['error' => 'Файл пустой.'];
        }

        if ($size > $this->size_limit) {
            return ['error' => 'Файл слижком большой.'];
        }

        $path_info = pathinfo($this->file->getName());
        //$filename = $path_info['filename'];
        $filename = md5(uniqid());
        $ext = @$path_info['extension'];		// hide notices if extension is empty

        if($this->allowed_extensions && !in_array(strtolower($ext), $this->allowed_extensions)){
            $these = implode(', ', $this->allowed_extensions);
            return ['error' => 'Файл имеет некорректный формат, формат должен соответствовать: '. $these . '.'];
        }

        if(!$replace_old_file) {
            /// don't overwrite previous files that were uploaded
            while (file_exists($upload_directory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }

        if ($this->file->save($upload_directory . $filename . '.' . $ext)){
            return ['success' => true, 'newFilename' => $filename . '.' . $ext];
        } else {
            return ['error' => 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered'];
        }
    }
}