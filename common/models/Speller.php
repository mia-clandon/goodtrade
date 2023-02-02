<?php

namespace common\models;

/**
 * This is the model class for table "speller".
 *
 * @property integer $id
 * @property string $word
 * @property string $corrected
 * @author Артём Широких kowapssupport@gmail.com
 */
class Speller extends Base {

    public static function tableName() {
        return 'speller';
    }

    public function rules() {
        return [
            [['word', 'corrected'], 'required'],
            [['word', 'corrected'], 'string', 'max' => 255],
            [['word'], 'unique'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => '#',
            'word' => 'Исходное слово',
            'corrected' => 'Исправленное слово',
        ];
    }
}