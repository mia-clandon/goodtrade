<?php

namespace common\models\firms;

use common\models\Base;
use common\models\User;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * This is the model class for table "firms_phones".
 *
 * @property integer $firm_id
 * @property string $phone
 */
class Phone extends Base {

    public static function tableName() {
        return 'firms_phones';
    }

    public function rules() {
        return [
            [['phone'], 'checkUsedPhone'],
            [['firm_id', 'phone'], 'required', 'when' => function($model) {
                /** @var $model static */
                return !empty($model->phone);
            }],
            [['firm_id'], 'integer'],
            [['phone'], 'string', 'max' => 60],
            [['firm_id', 'phone'], 'unique', 'targetAttribute' => ['firm_id', 'phone'],
                    'message' => 'The combination of Firm ID and Phone has already been taken.'],
        ];
    }

    public function preparePhone($phone) {
        return (int)preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * Метод форматирует телефон приводя к международному формату.
     * @return string
     */
    public function getInternationalFormatPhone() {
        $phone_util = PhoneNumberUtil::getInstance();
        try {
            $swiss_number_proto = $phone_util->parse($this->phone, "RU");
        }
        catch (NumberParseException $e) {
            return $this->phone;
        }
        return $phone_util->format($swiss_number_proto, PhoneNumberFormat::INTERNATIONAL);
    }

    /**
     * Валидация номера телефона у организации.
     * @param string $attribute
     * @return bool
     */
    public function checkUsedPhone($attribute) {
        $phone = $this->getAttribute($attribute);
        $phone = $this->preparePhone($phone);
        $user = User::get();
        $has_with_phone = User::find()
            ->where(['phone' => $phone,]);
        if ($user) {
            // у текущего пользователя не считаю номер.
            $has_with_phone->andWhere(['!=', 'id', $user->id]);
        }
        $has_with_phone = $has_with_phone->count();
        $error_message = 'Номер телефона "'.$phone.'" уже используется в системе.';
        // телефон используется у пользователя в профиле.
        if ($has_with_phone) {
            $this->addError($attribute, $error_message);
            return false;
        }
        $has_from_firms = Phone::find()
            ->where(['phone' => $phone])
            ->andWhere(['!=', 'firm_id', $this->firm_id])
            ->count();
        // телефон используется у организации.
        if ($has_from_firms) {
            $this->addError($attribute, $error_message);
            return false;
        }
        return true;
    }

    public function attributeLabels() {
        return [
            'firm_id' => 'Firm ID',
            'phone' => 'Phone',
        ];
    }
}