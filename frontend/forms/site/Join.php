<?php /** @noinspection PhpIncompatibleReturnTypeInspection */

namespace frontend\forms\site;

use common\libs\form\Form;
use common\libs\StringHelper;
use common\models\User;
use frontend\components\form\controls\{Activity,
    Email,
    Image,
    Input,
    Location,
    Phone,
    SearchBin,
    SearchCompany,
    TextArea
};
use frontend\models\form\Join as JoinModel;
use yii\helpers\Json;

/**
 * TODO: перетянуть контролы на новые.
 * Class Join
 * Форма регистрации организации.
 * @package frontend\forms\site
 * @author Артём Широких kowapssupport@gmail.com
 */
class Join extends Form
{

    private ?SearchCompany $company_title_control = null;
    private ?TextArea $company_description_control = null;
    private ?SearchBin $company_bin_control = null;

    private ?Image $image_control = null;
    private ?Location $location_control = null;
    private ?Activity $activity_control = null;
    private ?Input $legal_address_control = null;
    private ?Phone $phone_control = null;
    private ?Email $email_control = null;
    private ?Input $bank_control = null;
    private ?Input $bik_control = null;
    private ?Input $iik_control = null;
    private ?Input $kbe_control = null;
    private ?Input $knp_control = null;

    protected function initControls(): void
    {
        $this->addClass('join-user-form');
        $this->addDataAttribute('cabinet', \Yii::$app->urlManager->createUrl(['cabinet']));
        $this->registerFirstStepControls();
    }

    /**
     * Регистрирует контролы первого шага регистрации.
     */
    protected function registerFirstStepControls(): void
    {
        // название и бин компании.
        $this->registerControl($this->getCompanyTitleControl());
        $this->registerControl($this->getCompanyBinControl());

        // фото организации.
        $this->registerControl($this->getImageControl()->setTitle("Логотип"));

        // сферы деятельности.
        $this->registerControl($this->getActivityControl());

        // информация о местонахождении.
        $this->registerControl($this->getLocationControl());

        // юридический адрес организации.
        $this->registerControl($this->getLegalAddress());

        // телефоны организации.
        $this->registerControl($this->getPhoneControl());

        // почтовые адреса.
        $this->registerControl($this->getEmailControl());

        $this->registerControl($this->getCompanyDescriptionControl());

        # реквизиты
        $this->registerControl($this->getBankControl());
        $this->registerControl($this->getBikControl());
        $this->registerControl($this->getIikControl());
        $this->registerControl($this->getKbeControl());
        $this->registerControl($this->getKnpControl());
    }

    protected function getCompanyTitleControl(): SearchCompany
    {
        if (null === $this->company_title_control) {
            $this->company_title_control = (new SearchCompany())
                ->setName('company_title')
                ->setTitle('Название компании')
                ->setPlaceholder('Введите название вашей компании')
                ->addAttribute('autocomplete', 'off');ё
        }
        return $this->company_title_control;
    }

    protected function getCompanyDescriptionControl(): TextArea
    {
        if (null === $this->company_description_control) {
            $this->company_description_control = (new TextArea())
                ->setName('company_text')
                ->setPlaceholder('Введите описание вашей организации')
                ->setTitle('Описание');
        }
        return $this->company_description_control;
    }

    protected function getCompanyBinControl(): SearchBin
    {
        if (null === $this->company_bin_control) {
            $this->company_bin_control = (new SearchBin())
                ->setName('company_bin')
                ->setTitle('БИН')
                ->setPlaceholder('Введите ваш БИН');
            if ($this->hasTemporaryBin()) {
                $this->company_bin_control->setValue(
                    $this->getUser()->temp_bin
                );
            }
        }
        return $this->company_bin_control;
    }

    protected function getBankControl(): Input
    {
        if (null === $this->bank_control) {
            $this->bank_control = (new Input())
                ->setName('company_bank')
                ->setTitle('Банк')
                ->setPlaceholder('Введите банк бенефициара');
        }
        return $this->bank_control;
    }

    protected function getBikControl(): Input
    {
        if (null === $this->bik_control) {
            $this->bik_control = (new Input())
                ->setName('company_bik')
                ->setTitle('БИК')
                ->setPlaceholder('Введите ваш БИК');
        }
        return $this->bik_control;
    }

    protected function getIikControl(): Input
    {
        if (null === $this->iik_control) {
            $this->iik_control = (new Input())
                ->setName('company_iik')
                ->setTitle('ИИК')
                ->setPlaceholder('Введите ваш ИИК');
        }
        return $this->iik_control;
    }

    protected function getKbeControl(): Input
    {
        if (null === $this->kbe_control) {
            $this->kbe_control = (new Input())
                ->setName('company_kbe')
                ->setTitle('КБЕ')
                ->setPlaceholder('Введите КБЕ');
        }
        return $this->kbe_control;
    }

    protected function getKnpControl(): Input
    {
        if (null === $this->knp_control) {
            $this->knp_control = (new Input())
                ->setName('company_knp')
                ->setTitle('КНП')
                ->setPlaceholder('Введите КНП');
        }
        return $this->knp_control;
    }

    protected function getLegalAddress(): Input
    {
        if (null === $this->legal_address_control) {
            $this->legal_address_control = (new Input())
                ->setName('company_legal_address')
                ->setTitle('Юридический адрес')
                ->setPlaceholder('Ваш юридический адрес')
                ->setInputType(Input::GEO_TYPE);
        }
        return $this->legal_address_control;
    }

    protected function getLocationControl(): Location
    {
        if (null === $this->location_control) {
            $this->location_control = (new Location())
                ->setName('company_location')
                ->setTitle('Регион');
        }
        return $this->location_control;
    }

    protected function getPhoneControl(): Phone
    {
        if (null === $this->phone_control) {
            /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
            $this->phone_control = (new Phone())
                ->setName('company_phone')
                ->setTitle('Телефон')
                ->setPlaceholder('+7 (777) 707 00 77')
                // телефон из профиля пользователя.
                ->setValue([$this->getUser()->phone_real]);
        }
        return $this->phone_control;
    }

    protected function getEmailControl(): Email
    {
        if (null === $this->email_control) {
            /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
            $this->email_control = (new Email())
                ->setName('company_email')
                ->setTitle('Email')
                ->setPlaceholder('Введите ваш email')
                // email из профиля пользователя.
                ->setValue([$this->getUser()->email]);
        }
        return $this->email_control;
    }

    protected function getImageControl(): Image
    {
        if (null === $this->image_control) {
            $this->image_control = (new Image())
                ->setName('company_image');
        }
        return $this->image_control;
    }

    protected function getActivityControl(): Activity
    {
        if (null === $this->activity_control) {
            $this->activity_control = (new Activity())
                ->addClass('activity-control')
                ->setName('company_activity')
                ->setTitle('Сфера деятельности')
                ->setLabelTip('Максимум 3 сферы деятельности');
        }
        return $this->activity_control;
    }

    /**
     * Так как в форму устанавливаются данные, которые могут быть JSON
     * нужно их разобрать и установить в форму правильно.
     * @param array $data
     * @return Join
     */
    public function setFormData(array $data): static
    {
        $form_data = [];
        foreach ($data as $property => $value) {
            if (is_string($value) && StringHelper::isJson($value)) {
                $form_data[$property] = Json::decode($value);
            } else {
                $form_data[$property] = $value;
            }
        }
        return parent::setFormData($form_data);
    }

    private function getUser(): ?User
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return \Yii::$app->getUser()->getIdentity();
    }

    //Ввели ли БИН на первом шаге регистрации.
    public function hasTemporaryBin(): bool
    {
        return !empty($this->getUser()->temp_bin);
    }

    public function getTemporaryBin(): int
    {
        return $this->getUser()->temp_bin;
    }

    //Заполняет модель, которая работает с формой регистрации данными.
    public function save(): bool
    {
        /** @var JoinModel $model */
        $model = $this->getModel();

        foreach ($this->getControlsData() as $attribute_name => $value) {
            if (in_array($attribute_name, $model->attributes())) {
                $model->{$attribute_name} = $value;
            }
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $model->save();
        if (!$result) {
            $errors = $model->getErrors();
            $this->populateErrorsFromAR($errors);
        }
        return $result;
    }
}
