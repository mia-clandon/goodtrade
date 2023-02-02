<?php

namespace common\libs;

use yii\web\UploadedFile as BaseUploadedFile;

/**
 * Class UploadedFile
 * Прослойка UploadedFile.
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class UploadedFile extends BaseUploadedFile {

    const FILES_NAME_KEY        = 'name';
    const FILES_ERROR_KEY       = 'error';
    const FILES_SIZE_KEY        = 'size';
    const FILES_TMP_NAME_KEY    = 'tmp_name';
    const FILES_TYPE_KEY        = 'type';

    /**
     * Метод добавляет в $_FILES данные о новом файле для загрузки.
     * @param string $name
     * @param string $path
     */
    public static function addFileToFilesData($name, $path) {
        $data = self::createArrayLikeFiles($path);
        if ($data[self::FILES_ERROR_KEY] != UPLOAD_ERR_NO_TMP_DIR) {
            $_FILES[$name] = $data;
        }
    }

    /**
     * Метод создаёт массив с данными о файле на подобии $_FILES;
     * TODO: массив путей.
     * @param string $path
     * @return array
     */
    public static function createArrayLikeFiles($path) {
        if (!file_exists($path)) {
            return [
                self::FILES_NAME_KEY => '',
                self::FILES_ERROR_KEY => UPLOAD_ERR_NO_TMP_DIR,
                self::FILES_SIZE_KEY => 0,
                self::FILES_TMP_NAME_KEY => '',
                self::FILES_TYPE_KEY => '',
            ];
        }
        return [
            self::FILES_NAME_KEY => pathinfo($path, PATHINFO_FILENAME). '.'. pathinfo($path, PATHINFO_EXTENSION),
            self::FILES_ERROR_KEY => 0,
            self::FILES_SIZE_KEY => filesize($path),
            self::FILES_TMP_NAME_KEY => $path,
            self::FILES_TYPE_KEY => mime_content_type($path),
        ];
    }
}