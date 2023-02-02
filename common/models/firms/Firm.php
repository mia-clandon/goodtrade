<?php /** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpMissingParamTypeInspection */
/** @noinspection PhpSameParameterValueInspection */

/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection */

namespace common\models\firms;

use common\libs\manticore\Client;
use common\libs\RegionHelper;
use common\models\{Base,
    Category,
    firms\Categories as FirmCategories,
    goods\Categories as ProductCategories,
    goods\Product,
    Location,
    User
};
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\{ActiveQuery, Expression, Query};
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "firm".
 *
 * @property int $id
 * @property int $status
 * @property string $title
 * @property string $text
 * @property int $profile_id
 * @property int $user_id
 * @property int $country_id
 * @property int $region_id
 * @property int $city_id
 * @property int $is_top
 * @property string $legal_address
 * @property string|null $bin
 * @property string $bank
 * @property string $bik
 * @property string $iik
 * @property string $kbe
 * @property string $image
 * @property string $knp
 * @author Артём Широких kowapssupport@gmail.com
 */
class Firm extends Base {

    #region - Статусы организации.
    // Организация активна.
    const STATUS_ENABLED = 1;
    // Организация на модерации.
    const STATUS_MODERATION = 0;
    #endregion;

    const FIELD_PRODUCT_COUNT = 'product_count';
    const FIELD_CATEGORY_ID = 'category_id';
    const FIELD_CATEGORY_TITLE = 'title';

    public const FIRM_PRODUCT_LIMIT = 6;

    public array|null $categories = null;
    public array|null $phone = null;
    public array|null $email = null;

    /** @var string Свойство модели хранящие фото. */
    protected string $image_property = 'image';

    /** @var array - список id товаров. */
    private array $product_ids = [];

    /** @var bool Нужно ли выполнять действия в afterSave */
    protected bool $need_call_after_save = true;

    public static function tableName(): string
    {
        return 'firm';
    }

    public static function indexName(): string
    {
        return 'firms';
    }

    public function rules(): array
    {
        return [
            [['title', 'bin'], 'required', 'skipOnEmpty' => true],
            [['status', 'profile_id', 'user_id', 'country_id', 'is_top', 'region_id', 'city_id'], 'integer'],
            [['legal_address', 'text'], 'string'],
            [['email'], 'emailsValidator'],
            [['phone'], 'phonesValidator'],
            [['title', 'image', 'bin', 'bank', 'bik', 'iik', 'kbe', 'knp'], 'string', 'max' => 255],
            [['bin'], 'unique',],
            [['bin'], 'string', 'min' => 12, 'max' => 12,],
        ];
    }

    public function getStatuses(): array
    {
        return [
            self::STATUS_ENABLED => 'Включена',
            self::STATUS_MODERATION => 'На модерации',
        ];
    }

    //Статус организации.
    public function getCurrentStatusText(): string
    {
        $statuses = $this->getStatuses();
        if (array_key_exists($this->status, $statuses)) {
            return $statuses[$this->status];
        }
        return '';
    }

    public function isActiveStatus(): bool
    {
        return (int)$this->status === self::STATUS_ENABLED;
    }

    public function getTitle(): string
    {
        return htmlspecialchars((string)$this->title);
    }

    public function isTopSeller(): bool
    {
        return (bool)$this->is_top;
    }

    public function getDescription(): string
    {
        return htmlspecialchars((string)$this->text);
    }

    public function getImage(): string
    {
        return (string)$this->image;
    }

    //Моя организация ?
    public function isMine(): bool
    {
        if (\Yii::$app->getUser()->isGuest) {
            return false;
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->id === Firm::get()->id;
    }

    /**
     * Метод возвращает товары по установленным product_ids.
     * @return ActiveQuery
     * @see \common\models\firms\Firm::setProductIds
     */
    public function getProductByIds(): ActiveQuery
    {
        return Product::find()
            ->where(['id' => $this->product_ids])
            ->orderBy(new Expression("FIND_IN_SET(id, '" . implode(',', $this->product_ids) . "')"));
    }

    /**
     * Валидирует приходящие $this->email через модель Email.
     * @param string $attribute
     * @return bool
     */
    public function emailsValidator(string $attribute): bool
    {
        if (!$this->email) {
            return true;
        }
        foreach ($this->email as $email) {

            $email_entity = new Email();
            $email_entity->email = $email;
            $email_entity->validate(['email']);

            if ($email_entity->hasErrors('email')) {
                $this->populateErrors($attribute, $email_entity->getErrors());
            }
        }
        return empty($this->getErrors($attribute));
    }

    /**
     * Валидирует приходящие $this->phone через модель Phone.
     * @param string $attribute
     * @return bool
     */
    public function phonesValidator(string $attribute): bool
    {
        if (!$this->phone) {
            return true;
        }
        foreach ($this->phone as $phone) {

            $phone_entity = new Phone();
            $phone_entity->phone = $phone;
            $phone_entity->validate(['phone']);

            if ($phone_entity->hasErrors('phone')) {
                $this->populateErrors($attribute, $phone_entity->getErrors());
            }
        }
        return empty($this->getErrors($attribute));
    }

    //Пополняет ошибки в модель.
    private function populateErrors(string $attribute, array $error_list = []): self
    {
        foreach ($error_list as $errors) {
            if (is_array($errors)) {
                foreach ($errors as $error_message) {
                    $this->addError($attribute, $error_message);
                }
            } else {
                $this->addError($attribute, $errors);
            }
        }
        return $this;
    }

    public function getOwner(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getCity(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['id' => 'city_id']);
    }

    public function getCategories(): ActiveQuery
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->hasMany(Category::class, ['id' => 'activity_id'])
            ->viaTable(FirmCategories::tableName(), ['firm_id' => 'id']);
    }

    public function getPhones(): ActiveQuery
    {
        return $this->hasMany(Phone::class, ['firm_id' => 'id']);
    }

    public function getEmails(): ActiveQuery
    {
        return $this->hasMany(Email::class, ['firm_id' => 'id']);
    }

    /**
     * Метод возвращает Firm организации, если её нет - создаёт пустую.
     * @throws Exception
     */
    public static function get(): self
    {
        $user = User::get();
        if (is_null($user)) {
            throw new Exception('Пользователь не авторизован.');
        }
        /** @var static $firm */
        $firm = $user->getFirm()->one();
        if (!$firm) {
            $firm = new Firm();
            $firm->user_id = $user->id;
            $firm->bin = null;
            $firm->status = self::STATUS_MODERATION;
            $firm->save(true, ['user_id', 'bin', 'status']);
        }
        return $firm;
    }

    //Метод проверяет, существует ли организация у пользователя.
    public static function hasFirm(): bool
    {
        $user = User::get();
        if (is_null($user)) {
            throw new Exception('Пользователь не авторизован.');
        }
        /** @var static $firm */
        $firm = $user->getFirm()->one();
        return !is_null($firm);
    }

    //Возвращает идентификаторы категорий.
    public function getCategoryIds(): array
    {
        $categories = $this->getCategories()
            ->select('id')
            ->asArray()
            ->all();
        return ArrayHelper::getColumn($categories, 'id', []);
    }

    //Метод возвращает категории в которых у организации есть товар и количество товаров по категориям.
    public function getProductCategoriesData(): array
    {
        $categories = (new Query())
            ->select(['COUNT(*) AS `product_count`', 'p.category_id', 'c.title'])
            ->from(ProductCategories::tableName() . ' p')
            ->where(['p.firm_id' => $this->id])
            ->leftJoin(Category::tableName() . ' c', 'c.id = p.category_id')
            ->groupBy(['p.category_id'])
            ->all();

        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                self::FIELD_PRODUCT_COUNT => (int)$category[self::FIELD_PRODUCT_COUNT],
                self::FIELD_CATEGORY_ID => (int)$category[self::FIELD_CATEGORY_ID],
                self::FIELD_CATEGORY_TITLE => $category[self::FIELD_CATEGORY_TITLE],
            ];
        }
        return $data;
    }

    public function getLocation(bool $with_legal_address = true): string
    {
        $region_helper = RegionHelper::i()
            ->setCountryId($this->country_id)
            ->setCityId($this->city_id)
            ->setRegionId($this->region_id);
        if ($this->legal_address && $with_legal_address) {
            $region_helper->setAddress($this->legal_address);
        }
        return $region_helper->get();
    }

    //Обновление категорий организации. (возвращает ошибки.)
    public function updateCategories(bool $remove_not_exist = true): array
    {
        $categories = (array)$this->categories;
        if ($remove_not_exist && !is_null($this->categories)) {
            // удаляю связи которые есть в базе, но нет в $items;
            $current_firm_categories = FirmCategories::find()->where(['firm_id' => $this->id])
                ->select(['activity_id'])
                ->asArray()
                ->all();
            $current_firm_categories = ArrayHelper::getColumn($current_firm_categories, 'activity_id');
            $for_remove_relations = array_diff($current_firm_categories, $categories);
            FirmCategories::deleteAll(['firm_id' => $this->id, 'activity_id' => $for_remove_relations]);
        }
        $errors = [];
        foreach ($categories as $category_id) {
            $firms_categories = new FirmCategories();
            $firms_categories->activity_id = $category_id;
            $firms_categories->firm_id = $this->id;
            $result = $firms_categories->save();
            if (!$result) {
                $errors = ArrayHelper::merge($firms_categories->getErrors(), $errors);
            }
        }
        return $errors;
    }

    //Метод обновляет телефоны организации. (возвращает ошибки.)
    private function updatePhones(bool $remove_not_exist = true): array
    {
        $phones = (array)$this->phone;
        if ($remove_not_exist && !is_null($this->phone)) {
            // удаляю связи которые есть в базе, но нет в $items;
            $current_firm_phones = Phone::find()->where(['firm_id' => $this->id])
                ->select(['phone'])
                ->asArray()
                ->all();
            $current_firm_phones = ArrayHelper::getColumn($current_firm_phones, 'phone');
            $for_remove_relations = array_diff($current_firm_phones, $phones);
            Phone::deleteAll(['firm_id' => $this->id, 'phone' => $for_remove_relations]);
        }
        $errors = [];
        foreach ($phones as $phone) {
            $phone_entity = new Phone();
            $phone_entity->firm_id = $this->id;
            $phone_entity->phone = strval($phone);
            $result = $phone_entity->save();
            if (!$result) {
                $errors = ArrayHelper::merge($phone_entity->getErrors(), $errors);
            }
        }
        return $errors;
    }

    //Метод обновляет Email организации. (возвращает ошибки.)
    private function updateEmails(bool $remove_not_exist = true): array
    {
        $emails = (array)$this->email;
        if ($remove_not_exist && !is_null($this->email)) {
            // удаляю связи которые есть в базе, но нет в $items;
            $current_firm_emails = Email::find()->where(['firm_id' => $this->id])
                ->select(['email'])
                ->asArray()
                ->all();
            $current_firm_emails = ArrayHelper::getColumn($current_firm_emails, 'email');
            $for_remove_relations = array_diff($current_firm_emails, $emails);
            Email::deleteAll(['firm_id' => $this->id, 'email' => $for_remove_relations]);
        }
        $errors = [];
        foreach ($emails as $email) {
            $email_entity = new Email();
            $email_entity->firm_id = $this->id;
            $email_entity->email = $email;
            $result = $email_entity->save();
            if (!$result) {
                $errors = ArrayHelper::merge($email_entity->getErrors(), $errors);
            }
        }
        return $errors;
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = self::find();
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
    }

    //Обновление записи в индексе.
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->need_call_after_save) {

            $this->updateCategories();
            $this->updatePhones();
            $this->updateEmails();
            $this->updateIndex();

            // Привязываю профиль если такой есть.
            /** @var Profile $profile */
            $profile = Profile::find()->where(['bin' => $this->bin])->one();
            if ($profile) {
                $this->profile_id = $profile->id;
                $this->need_call_after_save = false;
                $this->save();
            }
        }
    }

    public function beforeSave($insert): bool
    {
        if ($this->need_call_before_save) {
            if ($saved = parent::beforeSave($insert)) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $this->uploadImage('company');
            }
            if (empty($this->bin)) {//не заполнен БИН!..
                $this->bin = null;
            }
            return $saved;
        }
        return parent::beforeSave($insert);
    }

    //Обновление организации в индексе.
    public function updateIndex(): bool
    {
        ini_set('memory_limit', '2048M');
        $row = [
            'id' => (int)$this->id,
            'm_id' => (int)$this->id,
            'status' => (int)$this->status,
            'profile_id' => (int)$this->profile_id,
            'user_id' => (int)$this->user_id,
            'country_id' => (int)$this->country_id,
            'region_id' => (int)$this->region_id,
            'city_id' => (int)$this->city_id,
            'bin' => (int)$this->bin,
            'title' => (string)$this->title,
            'legal_address' => (string)$this->legal_address,
        ];

        $this->addDataToIndexRow(array_map('intval', $this->getCategoryIds()), $row, 'activity');

        return (new Client())->saveRow(self::indexName(), $row);
    }

    //Переданная строка - БИН?
    public function isBIN(string $string): bool
    {
        return is_numeric($string) && strlen($string) == 12;
    }

    //Метод проверяет, используется ли эта организация в товарах.
    public function useFirmInProduct(): bool
    {
        return (bool)Product::find()->where(['firm_id' => $this->id])->count() > 0;
    }

    //Устанавливает телефоны в модель.
    public function setPhones(array $phones): self
    {
        $phones = array_filter($phones, function ($value) {
            return !empty($value);
        });
        if (empty($phones)) {
            $this->phone = [];
        }
        foreach ($phones as &$phone) {
            $phone = (string)preg_replace('/[^0-9]/', '', $phone);
        }
        unset($phone);
        $this->phone = array_unique($phones);
        return $this;
    }

    //Устанавливает Email'ы в модель.
    public function setEmails(array $emails): self
    {
        $emails = array_filter($emails, function ($value) {
            return !empty($value);
        });
        if (empty($emails)) {
            $this->email = [];
        }
        $this->email = array_unique($emails);
        return $this;
    }

    public function setBIN(string $bin): self
    {
        if (!empty($bin)) {
            $this->bin = trim($bin);
        }
        return $this;
    }

    public function setCityId(int $city_id): self
    {
        $this->city_id = $city_id;
        return $this;
    }

    public function setRegionId(int $region_id): self
    {
        $this->region_id = $region_id;
        return $this;
    }

    public function setCountryId(int $country_id): self
    {
        $this->country_id = $country_id;
        return $this;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    //Устанавливает категории в модель.
    public function setCategories(array $categories): self
    {
        $categories = array_filter($categories, function ($value) {
            return !empty($value);
        });
        if (empty($categories)) {
            $this->categories = [];
        }
        $this->categories = $categories;
        return $this;
    }

    public function setProductIds(array $product_ids): self
    {
        $this->product_ids = array_map('intval', $product_ids);
        return $this;
    }

    public function getProductIds(): array
    {
        return $this->product_ids;
    }

    public function beforeDelete(): bool
    {
        $this->clearImage();
        $this->setCategories([]);
        $this->setPhones([]);
        $this->setEmails([]);
        return parent::beforeDelete();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        (new Client())->deleteRow(self::indexName(), $this->id);
    }

    public function attributeLabels(): array
    {
        return [
            'id' => '#',
            'status' => 'Статус организации',
            'title' => 'Название организации',
            'image' => 'Логотип организации',
            'user_id' => 'Владелец (пользователь)',
            'profile_id' => 'Профиль организации',
            'country_id' => 'Страна',
            'region_id' => 'Область',
            'city_id' => 'Город',
            'legal_address' => 'Юридический адрес',
            'is_top' => 'Топовый продавец ?',
            'bin' => 'БИН',
            'bank' => 'Банк бенефициара',
            'bik' => 'БИК',
            'iik' => 'ИИК',
            'kbe' => 'КБЕ',
            'knp' => 'КНП',
        ];
    }

    //Проверяет роль пользователя.
    public function isAdmin(): bool
    {
        $auth_manager = \Yii::$app->getAuthManager();
        $assignment = $auth_manager->getAssignment('admin', $this->user_id);
        return !is_null($assignment);
    }
}
