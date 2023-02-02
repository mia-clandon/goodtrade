<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "chrono".
 * todo: переименовать таблицу / и модель.
 *
 * @property integer $id
 * @property string $title
 * @property int $type
 * @property int $firm_id
 * @property integer $created_at
 * @property integer $update_at
 *
 * @author yerganat
 */
class Chrono extends Base {


    #region - тиз записии в хронологии;
    const TYPE_PRODUCT = 1;
    const TYPE_COMMERSIAL_REQUEST = 2;
    const TYPE_CALENDAR = 3;
    #endregion;

    public static function tableName() {
        return 'chrono';
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules() {
        return [
            [['type', 'title'], 'required'],
            [['created_at', 'type', 'firm_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => '#',
            'title' => 'Заголовок',
            'type' => 'Вид: товар, коммерческий запрос, календарь...',
            'firm_id' => 'Фирма',
            'created_at' => 'Время создания',
            'updated_at' => 'Время обновления',
        ];
    }
    //todo: где типы ? return bool ??
    public static function log($type, $firm_id, $title) {
        $chrono = new Chrono();
        $chrono->type = $type;
        $chrono->title = $title;
        $chrono->firm_id = $firm_id;
        $chrono->save();
    }
}