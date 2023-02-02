<?php

namespace backend\forms\profile;

use backend\components\form\controls\TextArea;
use backend\components\form\Form;
use backend\components\form\controls\Input;
use backend\components\form\controls\Button;

use common\libs\traits\RegisterJsScript;
use common\models\firms\Profile;

use common\libs\form\validators\client\Number as NumberClient;
use common\libs\form\validators\client\Required as RequiredClient;

/**
 * Class Update
 * Форма обновления/создания профиля организации
 * @package backend\forms
 * @author Артём Широких kowapssupport@gmail.com
 */
class Update extends Form {

    use RegisterJsScript;

    /**
     * Инициализация компонентов формы.
     * @throws \Exception
     */
    protected function initControls(): void {
        parent::initControls();

        $title_input = (new Input())
            ->setName('title')
            ->setTitle('Название организации')
            ->setPlaceholder('Наименование предприятия')
            ->setJsValidator([(new RequiredClient())])
            ->addRule(['required'])
        ;
        $this->registerControl($title_input);

        $title_short_input = (new Input())
            ->setName('short_title')
            ->setTitle('Название организации (кратко)')
            ->setPlaceholder('Наименование предприятия (кратко)')
        ;
        $this->registerControl($title_short_input);

        $bin_input = (new Input())
            ->setName('bin')
            ->setPlaceholder('Бизнес-идентификационный номер')
            ->setTitle('БИН')
            ->setJsValidator([new RequiredClient(), new NumberClient()])
            ->addRule(['required'])
            ->addRule(['number'])
        ;
        $this->registerControl($bin_input);

        $oked_input = (new Input())
            ->setName('oked')
            ->setTitle('ОКЕД')
            ->setPlaceholder('Общий классификатор видов экономической деятельности')
            ->setJsValidator([new RequiredClient(), new NumberClient()])
            ->addRule(['required'])
            ->addRule(['number'])
        ;
        $this->registerControl($oked_input);

        $activity_input = (new Input())
            ->setName('activity')
            ->setTitle('Вид деятельности предприятия')
            ->setPlaceholder('Вид деятельности предприятия')
        ;
        $this->registerControl($activity_input);

        $kato_input = (new Input())
            ->setName('kato')
            ->setTitle('КАТО')
            ->setPlaceholder('Классификатор административно-территориальных объектов')
        ;
        $this->registerControl($kato_input);

        $locality_input = (new Input())
            ->setName('locality')
            ->setTitle('Населённый пункт')
            ->setPlaceholder('Населённый пункт')
            ->setJsValidator([(new RequiredClient())])
            ->addRule(['required'])
        ;
        $this->registerControl($locality_input);

        $krp_input = (new Input())
            ->setName('krp')
            ->setTitle('Классификатор размерности предприятия')
            ->setPlaceholder('Классификатор размерности предприятия')
        ;
        $this->registerControl($krp_input);

        $company_size_input = (new Input())
            ->setName('company_size')
            ->setTitle('Размер организации')
            ->setPlaceholder('Размер организации')
        ;
        $this->registerControl($company_size_input);

        $leader_input = (new Input())
            ->setName('leader')
            ->setTitle('Руководитель')
            ->setPlaceholder('Руководитель')
            ->setJsValidator([(new RequiredClient())])
            ->addRule(['required'])
        ;
        $this->registerControl($leader_input);

        $legal_address_input = (new TextArea())
            ->setName('legal_address')
            ->setTitle('Юридический адресс')
            ->setPlaceholder('Юридический адресс')
            ->setJsValidator([(new RequiredClient())])
            ->addRule(['required'])
        ;
        $this->registerControl($legal_address_input);

        $phone_text_area = (new TextArea())
            ->setName('phone')
            ->setTitle('Телефоны')
            ->setPlaceholder('Телефоны')
        ;
        $this->registerControl($phone_text_area);

        $email_input = (new Input())
            ->setName('email')
            ->setTitle('Email адрес')
            ->setPlaceholder('Email адрес')
        ;
        $this->registerControl($email_input);

        $site_input = (new Input())
            ->setName('site')
            ->setTitle('Сайт')
            ->setPlaceholder('Сайт')
        ;
        $this->registerControl($site_input);

        $button = (new Button())
            ->setName('submit')
            ->setContent('Сохранить организацию')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
        ;
        $this->registerControl($button);
    }

    /**
     * @return bool
     */
    public function save(): bool {
        $this->validate();
        if ($this->isValid()) {
            /** @var Profile $profile */
            $profile = $this->getModel();
            $form_data = $this->getFormData();
            foreach ($form_data as $property => $value) {
                if ($profile->hasAttribute($property)) {
                    $profile->{$property} = $value;
                }
            }
            return $profile->save();
        }
        else {
            return false;
        }
    }
}