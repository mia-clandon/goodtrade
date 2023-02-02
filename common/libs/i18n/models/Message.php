<?php

namespace common\libs\i18n\models;

use common\models\Base;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "message".
 *
 * @property int $id
 * @property string $language
 * @property string $translation
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Hint $id0
 * @author Артём Широких kowapssupport@gmail.com
 */
class Message extends Base {

    public static function tableName() {
        return 'translate_message';
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules() {
        return [
            [['id', 'language'], 'required'],
            [['id'], 'integer'],
            [['translation'], 'string'],
            [['language'], 'string', 'max' => 16],
            [['id', 'language'], 'unique', 'targetAttribute' => ['id', 'language']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Hint::class, 'targetAttribute' => ['id' => 'id']],
            [['created_at', 'updated_at', 'user_id',], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'language' => 'Language',
            'translation' => 'Translation',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getId(): ActiveQuery {
        return $this->hasOne(SourceMessage::class, ['id' => 'id']);
    }*/
}