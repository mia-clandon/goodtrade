<?php

namespace frontend\models;

use yii\base\Model;

/**
 * Class Uploader
 * Модель для загрузчика файлов.
 * @see \frontend\modules\api\controllers\UploaderController::actionUploadFile
 * @author Артём Широких kowapssupport@gmail.com
 */
class Uploader extends Model {

    /**
     * @var \yii\web\UploadedFile
     */
    public $file;
    /** @var string - сгенерированное название файла. */
    public $file_name;
    /** @var string - полный путь до файла. */
    public $file_path;

    public function rules() {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => [
                'xls', 'xlsx', 'csv',
            ]],
        ];
    }

    /**
     * Загрузчик файлов во временную папку.
     * @param boolean $change_base_name
     * @return bool
     */
    public function upload(bool $change_base_name = false) {
        if (!$this->validate()) {
            return false;
        }

        $file_name = $change_base_name ? md5($this->file->baseName. time()). '.' . $this->file->extension
            : $this->file->baseName. '.' . $this->file->extension;

        // сгенерированное название файла.
        $this->file_name = $file_name;

        // путь до файла.
        $file_path = \Yii::getAlias('@app/web/files/'). $file_name;
        $this->file->saveAs($file_path);
        $this->file_path = str_replace(\Yii::getAlias('@app/web'), '', $file_path);

        return true;
    }
}