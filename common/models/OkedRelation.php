<?php

namespace common\models;

/**
 * This is the model class for table "oked_relation".
 *
 * @property integer $id
 * @property integer $from_key
 * @property integer $to_key
 */
class OkedRelation extends Base {

    public static function tableName() {
        return 'oked_relation';
    }

    public function rules() {
        return [
            [['from_key', 'to_key'], 'integer'],
            [['from_key', 'to_key'], 'unique', 'targetAttribute' => ['from_key', 'to_key'], 'message' => 'The combination of From Key and To Key has already been taken.'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'from_key' => 'From Key',
            'to_key' => 'To Key',
        ];
    }
}