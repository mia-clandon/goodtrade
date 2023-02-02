<?php

namespace common\models;

/**
 * This is the model class for table "category_oked".
 *
 * @property integer $category_id
 * @property integer $oked
 * @author Артём Широких kowapssupport@gmail.com
 */
class CategoryOked extends Base {

    public static function tableName() {
        return 'category_oked';
    }

    public function rules() {
        return [
            [['category_id', 'oked'], 'integer'],
            [['category_id', 'oked'], 'unique', 'targetAttribute' => ['category_id', 'oked'], 'message' => 'The combination of # категории and № окэд has already been taken.'],
        ];
    }

    public function attributeLabels() {
        return [
            'category_id' => '# категории',
            'oked' => '№ окэд',
        ];
    }
}