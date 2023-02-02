<?php

namespace common\models\firms;

use common\models\Base;

/**
 * This is the model class for table "firms_categories".
 *
 * @property integer $id
 * @property integer $firm_id
 * @property integer $activity_id
 * @author Артём Широких kowapssupport@gmail.com
 */
class Categories extends Base {

    const TABLE_NAME = 'firms_categories';

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function rules() {
        return [
            [['firm_id', 'activity_id'], 'required'],
            [['firm_id', 'activity_id'], 'integer'],
            [['firm_id', 'activity_id'], 'unique', 'targetAttribute' => ['firm_id', 'activity_id'], 'message' => 'The combination of Firm ID and Phone has already been taken.'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => '#',
            'firm_id' => 'Firm ID',
            'activity_id' => 'Activity ID',
        ];
    }

    /**
     * Возвращает количество организаций по id категории.
     * @param int $category_id
     * @return int
     */
    public function getCountFirmsByCategoryId(int $category_id): int {
        return Categories::find()->where(['activity_id' => $category_id])->count();
    }
}