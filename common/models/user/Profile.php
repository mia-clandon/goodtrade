<?php

namespace common\models\user;

use common\models\Base;

/**
 * This is the model class for table "user_profile".
 *
 * @property integer $user_id
 * @property integer $notify_email
 * @author Артём Широких kowapssupport@gmail.com
 */
class Profile extends Base {

    public $primaryKey = 'user_id';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user_profile';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['notify_email'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'user_id' => 'Идентификатор пользователя',
            'notify_email' => 'Notify Email',
        ];
    }
}