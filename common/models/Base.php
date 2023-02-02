<?php

namespace common\models;

use common\libs\{sphinx\QueryBuilder, Storage, StringBuilder};
use common\modules\image\helpers\Image;
use yii\base\Exception;
use yii\db\{ActiveRecord, Expression};

/**
 * Базовая модель для работы с AR
 * Class Base
 * @property int $id
 * @package common\models
 * @author Артём Широких kowapssupport@gmail.com
 */
class Base extends ActiveRecord
{
    protected bool $need_call_after_save = true;
    protected bool $need_call_before_save = true;

    private ?string $image_for_upload = null;
    protected string $image_property = 'image';

    //Устанавливает фото для сохранения в модели.
    public function setImageForUpload(array $images, string $property = 'image'): self
    {
        // обнуляю текущую фотографию.
        $this->{$property} = '';

        $for_remove = arr_get_val($images, \frontend\components\form\controls\Image::FOR_REMOVE_KEY);
        if (isset($images[\frontend\components\form\controls\Image::FOR_REMOVE_KEY])) {
            unset($images[\frontend\components\form\controls\Image::FOR_REMOVE_KEY]);
        }
        $this->image_property = $property;
        $image = arr_get_val($images, 0);
        if (!empty($image)) {
            $this->image_for_upload = (string)$image;
            $this->image_property = $property;
            // для исключения ошибок валидации.
            if ($this->hasProperty($property)) {
                $this->{$property} = $image;
            }
        }

        if ($for_remove) {
            $this->clearImage();
        }
        return $this;
    }

    /**
     * Метод загружает фото и сохраняет его в модели,
     * при условии если в модель установлено фото.
     * @param string $storage
     * @return bool
     * @throws Exception
     */
    protected function uploadImage(string $storage): bool
    {
        if (!$storage) {
            throw new Exception('Нужно указать хранилище для хранения фото.');
        }
        if ($this->image_for_upload && $this->hasAttribute($this->image_property)) {
            $image_attr = $this->image_property;
            $uploaded_file = false;
            $base_name = urldecode(pathinfo($this->image_for_upload, PATHINFO_BASENAME));
            $file_path = \Yii::getAlias('@app/web/files/' . $base_name);
            if (file_exists($file_path)) {
                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                $path = Storage::getInstance()->generatePath($storage, $ext);
                $result = Storage::getInstance()->move($file_path, $path);
                if ($result) {
                    @unlink($file_path);
                    $uploaded_file = Storage::getInstance()->getRelativePath($path);
                }
            }
            // Удаление миниатюр - сохранение нового файла.
            if ($uploaded_file) {
                //Image::i()->removeAllThumbnails($this->getOldAttribute($image_attr), true);
                $this->{$image_attr} = (string)$uploaded_file;
                $this->setNeedCallBeforeSave(false);
                return $this->save();
            }
        }
        return true;
    }

    //Метод удаляет фото.
    public function clearImage(): bool
    {
        $image_attr = $this->image_property;
        if ($this->hasAttribute($this->image_property) && $this->{$image_attr}) {
            $image = $this->{$image_attr};
            if (is_string($image)) {
                Image::getInstance()->removeAllThumbnails($image, true);
                $this->{$image_attr} = '';
            }
        }
        return true;
    }

    //Массив с ошибками в строку.
    public function stringifyErrors(array $errors, string $comma = '<br />'): string
    {
        $error_string = new StringBuilder();
        foreach ($errors as $property => $error) {
            if (is_array($error)) {
                foreach ($error as $message) {
                    $error_string->add($message);
                    $error_string->add($comma);
                }
            } else {
                $error_string->add($error);
                $error_string->add($comma);
            }
        }
        return rtrim($error_string->get(), $comma);
    }

    //Метод добавляет в массив в котором содержатся данные для вставки в index новую запись (rt_attr_multi).
    protected function addDataToIndexRow(array $data, array &$row, $key)
    {
        if (!empty($data)) {
            $row[(string)$key] = new Expression('(' . implode(',', $data) . ')');
        }
    }

    public function removeSphinxIndex(): int
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $qb = new QueryBuilder(\Yii::$app->get('sphinx'));
        return $qb->deleteById(static::tableName(), $this->id);
    }

    //Для удаления зависимых записей в базе.
    public function clearRelations(): bool
    {
        return true;
    }

    public function setNeedCallAfterSave(bool $flag = true): self
    {
        $this->need_call_after_save = $flag;
        return $this;
    }

    public function setNeedCallBeforeSave(bool $flag = true): self
    {
        $this->need_call_before_save = $flag;
        return $this;
    }
}
