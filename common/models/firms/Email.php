<?php

namespace common\models\firms;

use common\models\Base;
use common\models\User;

/**
 * This is the model class for table "firms_emails".
 *
 * @property integer $firm_id
 * @property string $email
 */
class Email extends Base {

    public static function tableName() {
        return 'firms_emails';
    }

    public function rules() {
        return [
            [['email'], 'checkUsedEmail'],
            [['firm_id', 'email'], 'required', 'when' => function($model) {
                /** @var $model static */
                return !empty($model->email);
            }],
            [['firm_id'], 'integer'],
            [['email'], 'email', 'message' => 'Не корректный формат «Email» - "{value}"'],
            [['email'], 'string', 'max' => 60],
            [['firm_id', 'email'], 'unique', 'targetAttribute' => ['firm_id', 'email'], 'message' => 'The combination of Firm ID and Email has already been taken.'],
        ];
    }

    /**
     * @param string $attribute
     * @return bool
     */
    public function checkUsedEmail($attribute) {
        $email = $this->getAttribute($attribute);
        $user = User::get();
        $has_with_email = User::find()
            ->where(['email' => $email]);
        if ($user) {
            // у текущего пользователя не считаю email.
            $has_with_email->andWhere(['!=', 'id', $user->id]);
        }
        $has_with_email = $has_with_email->count();
        $error_message = 'Email "'.$email.'" уже используется в системе.';
        // Email используется у пользователя в профиле.
        if ($has_with_email) {
            $this->addError($attribute, $error_message);
            return false;
        }
        $has_from_firms = Email::find()
            ->where(['email' => $email])
            ->andWhere(['!=', 'firm_id', $this->firm_id])
            ->count();
        // Email используется у организации.
        if ($has_from_firms) {
            $this->addError($attribute, $error_message);
            return false;
        }
        return true;
    }

    public function attributeLabels() {
        return [
            'firm_id' => 'Firm ID',
            'email' => 'Email',
        ];
    }
}