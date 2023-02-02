<?php

namespace common\libs;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

use common\libs\traits\Singleton;

/**
 * Class Storage
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class Storage {
    use Singleton;

    const PATH_PART_LENGTH = 2;

    /**
     * Генерирует рандомный путь по настройкам в params.
     * @param $storage_name
     * @param $extension
     * @return bool|string
     * @throws Exception
     */
    public function generatePath($storage_name, $extension) {

        if (empty($storage_name)) {
            throw new Exception('Storage name must be set.');
        }

        $storage_conf = ArrayHelper::getValue(\Yii::$app->params, 'storage.directories.'.$storage_name);

        $directory = ArrayHelper::getValue($storage_conf, 'directory');
        if (is_null($storage_conf) || is_null($directory)) {
            throw new Exception(strtoupper($storage_name). ' storage conf not exist.');
        }

        // генерация под пути (вложенный путь в базовом хранилище).
        $path_length = ArrayHelper::getValue($storage_conf, 'length', 0);
        $sub_path = $this->generateRandomPath($path_length);

        // название базовой директории хранилища.
        $base_directory = ArrayHelper::getValue($storage_conf, 'base_directory', 'storage');

        // сбор пути.
        $path_builder = (new StringBuilder())
            ->setComma('/')
            ->add(\Yii::getAlias('@common/web'))
            ->add($base_directory)
            ->add($directory)
            ->add($sub_path)
            ->setComma('')
        ;

        $path = $path_builder->get();

        try {
            if (!mkdir($path, 0777, true)) {
                return false;
            }
            $file_name = (new StringBuilder())
                ->add($path)
                ->setComma('.')
                ->add($this->generateRandomFileName())
                ->add($extension, false)
                ->get();

            fopen($file_name , 'w');
            return $file_name;
        }
        catch (Exception $e) {
            \Yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Метод загружает в storage массив файлов.
     * @param array $images
     * @param string $storage_name
     * @return array
     */
    public function moveFilesToStorage(array $images, $storage_name) {
        $uploaded_files = [];
        foreach ($images as $image) {
            $base_name = urldecode(pathinfo($image, PATHINFO_BASENAME));

            // если файл находится на удаленном сервере.
            $file_host = ArrayHelper::getValue(parse_url($image), 'host');
            if ($file_host && $file_host !== BASE_DOMAIN) {
                // upload file to files.
                try {
                    if (Storage::i()->isRemoteFileExist($image)) {
                        $file = file_get_contents($image);
                        $extension = pathinfo($image, PATHINFO_EXTENSION);
                        $base_name = md5(pathinfo($image, PATHINFO_FILENAME).(string)time()).'.'.$extension;
                        $file_path = \Yii::getAlias('@app/web/files/').$base_name;
                        file_put_contents($file_path, $file);
                    }
                }
                catch (Exception $exception) {
                    \Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
                }
            }
            try {
                // перемещение файла в Storage.
                $file_path = \Yii::getAlias('@app/web/files/'. $base_name);
                if (file_exists($file_path)) {
                    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                    $path = Storage::getInstance()->generatePath($storage_name, $ext);
                    $result = Storage::getInstance()->move($file_path, $path);
                    if ($result) {
                        @unlink($file_path);
                        $uploaded_files[] = Storage::getInstance()->getRelativePath($path);
                    }
                }
            }
            catch (Exception $exception) {
                \Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
            }
        }
        return $uploaded_files;
    }

    /**
     * Относительный путь.
     * @param $path
     * @return mixed
     */
    public function getRelativePath($path) {
        return str_replace(\Yii::getAlias('@common/web'), '', $path);
    }

    /**
     * @return bool|string
     */
    public static function getStoragePath() {
        return \Yii::getAlias('@common/web/storage');
    }

    /**
     * @param string $file
     * @return string
     */
    public static function getRealFilePath(string $file): string {
        if (mb_strpos($file, 'storage') !== false) {
            return \Yii::getAlias('@common/web'). $file;
        }
        else {
            return static::getStoragePath(). '/'. $file;
        }
    }

    /**
     * @param $from
     * @param $to
     * @return bool
     */
    public function move($from, $to) {
        if (!is_writable($to)) {
            \Yii::error('Cannot write to destination file');
            return false;
        }
        if (!file_exists($from)) {
            \Yii::error('Move file not exist');
            return false;
        }
        return copy($from, $to);
    }

    /**
     * Метод проверяет, существует ли удалённый файл.
     * @param string $url
     * @return bool
     */
    public function isRemoteFileExist($url) {
        $url = (string)$url;
        // save file
        if (function_exists('curl_exec')) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_NOBODY, true);
            curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            return ($http_code >= 200 && $http_code < 300);
        }
        return false;
    }

    /**
     * Генерация рандомного пути
     * @param int $path_length
     * @return string - возвращает путь (строка)
     */
    private function generateRandomPath($path_length = 0) {
        $string_builder = new StringBuilder();
        for ($i = 0; $i < $path_length; $i++) {
            $string_builder->add(
                strtolower($this->generateRandomString(self::PATH_PART_LENGTH))
            );
            $string_builder->add('/');
        }
        $out = substr($string_builder->get(), 0, -1);
        return $out ? $out : '';
    }

    /**
     * Генерирует рандомную строку нужной длинны.
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Генерирует рандомную строку.
     * @return string
     */
    private function generateRandomFileName() {
        return md5(rand(0, 99).time());
    }
}