<?php

namespace common\models;

use common\models\firms\Firm;
use common\models\user\Profile;
use yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $phone
 * @property string $phone_real
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $temp_bin
 * @property string $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $auth_token
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 10;

    public string $re_password;

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => '#',
            'username' => 'Имя пользователя',
            'email' => 'Почта',
            'phone_real' => 'Телефон',
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'temp_bin' => 'Временный БИН',
        ];
    }

    public function rules(): array
    {
        return [
            [['username', 'email', 'phone', 'auth_token'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['auth_token'], 'string', 'max' => 32],
            [['temp_bin',], 'string', 'max' => 12],
        ];
    }

    public static function get(): ?self
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return Yii::$app->getUser()->getIdentity();
    }

    public function getFirm(): yii\db\ActiveQuery
    {
        return $this->hasOne(Firm::class, ['user_id' => 'id']);
    }

    public function getProfile(): ?Profile
    {
        if (!$this->isNewRecord) {
            $profile = Profile::findOne($this->id);
            if (!$profile) {
                $profile = new Profile();
                $profile->user_id = $this->id;
                $profile->save();
            }
            return $profile;
        }
        return null;
    }

    public function hasFirm(): bool
    {
        return Firm::find()->where(['user_id' => $this->getId()])->count() > 0;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $this->preparePhone($phone);
        $this->phone_real = (string)$phone;
        return $this;
    }

    public function preparePhone(string $phone): int
    {
        return (int)preg_replace('/[^0-9]/', '', $phone);
    }

    public function getPhone(): string
    {
        return $this->phone_real;
    }

    //Генерация случайного пароля.
    public static function generateRandomPassword(int $length = 6): string
    {
        $chars = md5(time());
        return substr(str_shuffle($chars), 0, $length);
    }

    public static function findIdentity($id): User|IdentityInterface|null
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return IdentityInterface|null
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by phone
     * @param string $phone
     * @return static|null
     */
    public static function findByPhone($phone) {
        return static::findOne(['phone' => $phone, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email
     * @param string $email
     * @return null|static
     */
    public static function findByEmail($email) {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId() {
        return $this->getPrimaryKey();
    }

    public function search($params): yii\data\ActiveDataProvider
    {
        $query = self::find();
        $dataProvider = new yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    public function getAuthKey() {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     * @return static
     */
    public function setPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        return $this;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Генерирует ключ для возможности авторизовать пользователя с админ панели на frontend окружении.
     * @return string
     */
    public static function generateAuthToken() {
        return md5(
            Yii::$app->security->generateRandomString() . '_' . time()
        );
    }

    /**
     * Метод обновляет токен авторизации пользователя.
     * @return bool|string
     */
    public function updateAuthToken() {
        $this->auth_token = static::generateAuthToken();
        return ($this->save(true, ['auth_token'])) ? $this->auth_token : false;
    }

    /**
     * Метод удаляет токен авторизации пользователя.
     * @return bool
     */
    public function removeAuthToken() {
        $this->auth_token = null;
        return $this->save(true, ['auth_token']);
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }
}