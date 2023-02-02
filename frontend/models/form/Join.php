<?php

namespace frontend\models\form;

use common\models\firms\Firm;
use common\models\Location;
use yii\base\{Exception, Model};

/**
 * Class Join
 * Данная модель работает с формой регистрации.
 * Сохраняет данные организации и первого товара.
 * @package frontend\models\form
 * @author Артём Широких kowapssupport@gmail.com
 */
class Join extends Model
{
    public const COMPANY_DATA_PREFIX = 'company_';

    public string $company_title = '';
    public string $company_text = '';
    public string $company_bin = '';
    public array $company_activity = [];
    public array $company_location = [];
    public string $company_legal_address = '';
    public array $company_phone = [];
    public array $company_email = [];
    public string $company_bank = '';
    public string $company_bik = '';
    public string $company_iik = '';
    public string $company_kbe = '';
    public string $company_knp = '';
    public array $company_image = [];

    //Метод проверяет, нужно ли сохранять профиль организации.
    private function needSaveCompany(): bool
    {
        return true;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getUserId(): int
    {
        $user_id = intval(\Yii::$app->getUser()->id);
        if (!$user_id) {
            throw new Exception('Пользователь не авторизован.');
        }
        return $user_id;
    }

    //Возвращает модель Firm, если у пользователя есть организация возвращает её.
    private function getFirmModel(): Firm
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return Firm::get();
    }

    /**
     * Создаёт профиль организации.
     * @param array $data
     * @return bool
     * @throws Exception
     */
    private function createCompany(array $data): bool
    {
        if (!$this->needSaveCompany()) {
            return false;
        }

        $company = $this->getFirmModel();
        $company->setAttributes($data);
        $company
            ->setUserId((int)$this->getUserId())
            ->setBIN((string)arr_get_val($data, 'bin'))
            ->setCountryId((int)Location::COUNTRY_KAZAKHSTAN)
            ->setRegionId((int)arr_get_val($data, 'location.' . \frontend\components\form\controls\Location::KEY_REGION_ID))
            ->setCityId((int)arr_get_val($data, 'location.' . \frontend\components\form\controls\Location::KEY_CITY_ID))
            ->setCategories(arr_get_val($data, 'activity', []))
            ->setPhones(arr_get_val($data, 'phone', []))
            ->setEmails(arr_get_val($data, 'email', []))
            ->setImageForUpload(arr_get_val($data, 'image', []));

        $result = $company->save();

        // ошибки валидации организации.
        $errors = $company->getErrors();
        foreach ($errors as $attribute => $messages) {
            foreach ($messages as $message) {
                $this->addError(self::COMPANY_DATA_PREFIX. $attribute, $message);
            }
        }
        return $result;
    }

    /**
     * Разбирает данные с формы регистрации и сохраняет модели.
     * @return boolean
     * @throws Exception
     */
    public function save(): bool
    {
        $company_data = [];

        foreach ($this->getAttributes() as $attribute => $value) {
            if (str_starts_with($attribute, self::COMPANY_DATA_PREFIX)) {
                $attr = str_replace(self::COMPANY_DATA_PREFIX, '', $attribute);
                $company_data[$attr] = $value;
            }
        }

        $this->createCompany($company_data);

        return empty($this->getErrors());
    }
}