<?php

namespace common\libs\uploader;

use yii\helpers\ArrayHelper;
use yii\image\drivers\Image;

use common\libs\StringBuilder;

/**
 * Class Handler
 * @package common\libs\uploader
 * Переопределенный загрузчик
 * @author Артём Широких kowapssupport@gmail.com
 */
class Handler extends UploadHandler {

    /**
     * Создание миниатюр.
     * @param $file_path
     * @param $file
     */
    protected function handle_image_file($file_path, $file) {
        // обработка версий изображения.
        foreach ($this->options['image_versions'] as $version => $options) {
            // создание миниатюр.
            if ($version == 'thumbnail') {

                $width = ArrayHelper::getValue($options, 'width', false);
                $height = ArrayHelper::getValue($options, 'height', false);

                // не обрабатываю фото без указанной ширины.
                if (!$width) continue;

                $type = ArrayHelper::getValue($options, 'type', 'NONE');

                if (!in_array($type, array_keys($this->getImageResizeTypes()))) {
                    $type = 'NONE';
                }

                $image = Image::factory($file_path, 'Imagick');
                $image->resize($width, $height, $this->getImageResizeType($type));

                // свойства миниатюры.
                $th = $this->thumbnailPath($file_path);
                $file_name = pathinfo($file_path, PATHINFO_FILENAME);
                $ext = pathinfo($file_path, PATHINFO_EXTENSION);

                // данные миниатюры.
                $th_name = md5($file_name).'.'.$ext;
                $th_path = (new StringBuilder())
                    ->add($th)
                    ->add($th_name)
                ;
                $image->save($th_path->get());
                // название свойства в $file
                $file->{$version} = $this->get_download_url(
                    $th_name, // название файла миниатюры
                    $version // версия
                );
                // относительный путь.
                $relative_path = $version. "_relative";
                // todo:
                $file->{$relative_path} = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file_path);
            }
        }
    }

    /**
     * Переопределил метод для замены имени загружаемого файла на уникальное.
     * @param $file_path
     * @param $name
     * @param $size
     * @param $type
     * @param $error
     * @param $index
     * @param $content_range
     * @return mixed|string
     */
    public function trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range) {
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $name = md5(pathinfo($name, PATHINFO_FILENAME).time()).'.'.$ext;
        return parent::trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range);
    }

    /**
     * @param $type
     * @return int|mixed
     */
    private function getImageResizeType($type) {
        $types = $this->getImageResizeTypes();
        return (isset($types[$type])) ? $types[$type] : 0x01;
    }

    /**
     * Возможные типы ресайзов.
     * @see yii/image/drivers/Kohana/Image.php
     * @return array
     */
    private function getImageResizeTypes() {
        return [
            'NONE'    => 0x01,
            'WIDTH'   => 0x02,
            'HEIGHT'  => 0x03,
            'AUTO'    => 0x04,
            'INVERSE' => 0x05,
            'PRECISE' => 0x06,
            'ADAPT'   => 0x07,
            'CROP'    => 0x08,
            'HORIZONTAL' => 0x11,
            'VERTICAL'   => 0x12,
        ];
    }

    /**
     * @param $file_path
     * @return $this|string
     */
    private function thumbnailPath($file_path) {
        $path = (new StringBuilder())
            ->setComma('/')
            ->add(pathinfo($file_path, PATHINFO_DIRNAME))
            ->add('thumbnail');
        $path = $path->get();
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

    /**
     * Инициализация.
     */
    protected function initialize() {
        // Без DELETE
        switch ($this->get_server_var('REQUEST_METHOD')) {
            case 'OPTIONS':
            case 'HEAD':
                $this->head();
                break;
            case 'GET':
                $this->get($this->options['print_response']);
                break;
            case 'PATCH':
            case 'PUT':
            case 'POST':
                $this->post($this->options['print_response']);
                break;
            default:
                $this->header('HTTP/1.1 405 Method Not Allowed');
        }
    }

    /**
     * @param $file
     */
    protected function set_additional_file_properties($file) {}
}