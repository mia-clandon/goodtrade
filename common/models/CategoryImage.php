<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

use common\modules\image\helpers\Image;

/**
 * This is the model class for table "category_images".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $image
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class CategoryImage extends ActiveRecord {

    public static function tableName() {
        return 'category_images';
    }

    public function rules() {
        return [
            [['category_id'], 'required'],
            [['category_id', 'position', 'created_at', 'updated_at'], 'integer'],
            [['image'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => '#',
            'category_id' => 'Идентификатор категории',
            'position' => 'Порядок фото',
            'image' => 'Путь до фотографии',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function beforeDelete() {
        $this->clearImage();
        return parent::beforeDelete();
    }

    /**
     * Удаляет фото.
     * @return bool
     */
    public function clearImage(): bool {
        if (null !== $this->image && is_string($this->image)) {
            Image::getInstance()->removeAllThumbnails($this->image, true);
            $this->image = null;
            return $this->save(true, ['image']);
        }
        return true;
    }
}