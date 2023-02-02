<?php

namespace common\models\seo;

use common\models\Base;

/**
 * This is the model class for table "seo_redirects".
 *
 * @property integer $id
 * @property string $old_url
 * @property string $new_url
 * @property string $status
 * @author Артём Широких kowapssupport@gmail.com
 */
class Redirects extends Base {

    const TABLE_NAME = 'seo_redirects';

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function rules() {
        return [
            [['old_url'], 'required'],
            [['status'], 'string'],
            [['old_url', 'new_url'], 'string', 'max' => 255],
            [['old_url'], 'unique'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'old_url' => 'Old Url',
            'new_url' => 'New Url',
            'status' => 'Status',
        ];
    }
}