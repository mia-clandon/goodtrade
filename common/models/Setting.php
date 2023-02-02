<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "setting".
 *
 * @property integer $id
 * @property string $title
 * @property string $name
 * @property string $value
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author yerganat
 */
class Setting extends Base {

    public static function tableName() {
        return 'setting';
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules() {
        return [
            [['name', 'title', 'value'], 'required'],
            [['name', 'title', 'value'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }
}