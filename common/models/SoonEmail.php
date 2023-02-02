<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "soon_email".
 *
 * @property integer $id
 * @property string $email
 * @property integer $created
 * @author Артём Широких kowapssupport@gmail.com
 */
class SoonEmail extends ActiveRecord {

    public static function tableName() {
        return 'soon_email';
    }

    public function rules() {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
            [['created'], 'integer'],
            [['email'], 'string', 'max' => 255],
            [['email'], 'unique', 'message' => 'Пользователь уже подписан на запуск'],
        ];
    }

    public function attributeLabels() {
        return ['id' => '#', 'email' => 'E-mail', 'created' => 'Created',];
    }
}