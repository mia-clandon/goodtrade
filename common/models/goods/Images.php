<?php

namespace common\models\goods;

use common\models\Base;
use common\modules\image\helpers\Image;
use common\modules\image\helpers\Image as ImageHelper;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "product_images".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $image
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 * @author Артём Широких kowapssupport@gmail.com
 */
class Images extends Base {

    const TABLE_NAME = 'product_images';

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules() {
        return [
            [['product_id', 'position', 'created_at', 'updated_at'], 'integer'],
            [['image'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => '#',
            'product_id' => 'Product ID',
            'image' => 'Путь к файлу',
            'position' => 'Порядок фото',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeDelete(): bool
    {
        $this->clearImage();
        return parent::beforeDelete();
    }

    //Удаляет фото.
    public function clearImage(): bool
    {
        if (null !== $this->image && is_string($this->image)) {
            Image::getInstance()->removeAllThumbnails($this->image, true);
            $this->image = null;
            return $this->save(true, ['image']);
        }
        return true;
    }

    /**
     * @param int|null $w
     * @param int|null $h
     * @return string
     */
    public function getImage(int $w = null, int $h = null): string {
        $image_resize = ImageHelper::i()->generateRelativeImageLink($this->image, $w, $h);
        if ($image_resize === false) {
            return '';
        }
        return $image_resize;
    }
}