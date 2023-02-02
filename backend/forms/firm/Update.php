<?php

namespace backend\forms\firm;

use yii\helpers\Url;

use backend\components\form\controls\Button;
use backend\components\form\controls\Checkbox;
use backend\components\form\controls\Image;
use backend\components\form\controls\Input;
use backend\components\form\controls\SearchSelect;
use backend\components\form\controls\Select;
use backend\components\form\controls\Selectize;
use backend\components\form\controls\TextArea;

use common\libs\form\components\Option;
use common\libs\form\Form;
use common\libs\form\validators\client\Length;
use common\libs\form\validators\client\Required;
use common\libs\traits\RegisterJsScript;
use common\models\Category;
use common\models\firms\Firm;
use common\models\firms\Profile;
use common\models\Location;
use common\models\User;

/**
 * Class Update
 * Форма обновления/создания организации
 * @package backend\forms
 * @author Артём Широких kowapssupport@gmail.com
 */
class Update extends Form {

    use RegisterJsScript;

    protected function registerFormAssets() {
        \Yii::$app->getView()->registerJs("
//            $('body').on('change', 'select[name=region_id]', function() {
//                var region_id = $(this).find('option:selected').val();
//            });
        ");
    }

    /**
     * Инициализация компонентов формы.
     * @throws \Exception
     */
    protected function initControls(): void {

        $this->registerJsScript();

        $title_component = (new Input())
            ->setName('title')
            ->setPlaceholder('Юридическое название организации')
            ->addAttribute('required', 'required')
            ->setJsValidator([new Required()])
        ;
        $this->registerControl($title_component);

        $status_control = (new Selectize())
            ->setTitle('Статус организации')
            ->setPlaceholder('Выберите статус организации')
            ->setName('status')
            ->setArrayOfOptions((new Firm())->getStatuses())
        ;
        $this->registerControl($status_control);

        $description_control = (new TextArea())
            ->setName('text')
            ->addAttributes(['rows' => 10])
            ->setPlaceholder('Описание организации')
        ;
        $this->registerControl($description_control);

        /** @var Firm $model */
        $model = $this->getModel();
        $image_component = (new Image())
            ->setTitle('Выберите логотип организации')
            ->setName('image')
            ->setRemoveAction(Url::to(['firm/remove-image']))
            ->setSizes(200, 100, 'AUTO')
            ->setAdditionalParams(['data-entity-id' => $model->id])
        ;
        $this->registerControl($image_component);

        $user_owner_component = (new SearchSelect())
            ->setName('user_id')
            ->setPlaceholder('Введите id либо e-mail пользователя')
            ->setUrl(Url::to(['firm/search-user']))
            ->setLabelField('email')
            ->setSearchField('email')
        ;
        $this->setUser($user_owner_component);
        $this->registerControl($user_owner_component);

        /*
        $profile_parser_component = (new SearchSelect())
            ->setName('profile_id')
            ->setPlaceholder('Введите название организации или БИН')
            ->setUrl(Url::to(['api/profile/find']))
            ->setLabelField('title')
            ->setSearchField('title')
            ->setQueryField('query')
            ->setRequestMethod(Form::METHOD_POST)
        ;
        $this->setProfile($profile_parser_component);
        $this->registerComponent($profile_parser_component);
        */

        $category_control = (new Selectize())
            ->setName('categories')
            ->setTitle('Сферы деятельности')
            ->setPlaceholder('Выберите сферы деятельности')
            ->setIsMultiple()
        ;
        $this->populateCategories($category_control);
        $this->registerControl($category_control);

        $top_firm_control = (new Checkbox())
            ->setName('is_top')
            ->setTitle('Топовый продавец ?')
        ;
        $this->registerControl($top_firm_control);

        $country_component = (new Selectize())
            ->setName('country_id')
        ;
        $this->populateCountries($country_component);
        $this->registerControl($country_component);

        $region_component = (new Selectize())
            ->setName('region_id')
        ;
        $this->populateRegions($region_component);
        $this->registerControl($region_component);

        $city_component = (new Selectize())
            ->setName('city_id')
        ;
        $this->populateCities($city_component);
        $this->registerControl($city_component);

        $legal_address_component = (new Input())
            ->setName('legal_address')
            ->setPlaceholder('Юридический адрес организации')
        ;
        $this->registerControl($legal_address_component);

        $bin_component = (new Input())
            ->setType(Input::TYPE_NUMBER)
            ->setName('bin')
            ->addAttribute('maxlength', 12)
            ->setPlaceholder('Бизнес-идентификационный номер')
            ->setJsValidator([
                (new Length())->addMax(12)
            ])
        ;
        $this->registerControl($bin_component);

        $bank_component = (new Input())
            ->setPlaceholder('Введите банк бенефициара')
            ->setName('bank')
        ;
        $this->registerControl($bank_component);

        $bik_component = (new Input())
            ->setPlaceholder('Банковский идентификационный код')
            ->setName('bik')
        ;
        $this->registerControl($bik_component);

        $iik_component = (new Input())
            ->setPlaceholder('Индивидуальный идентификационный код')
            ->setName('iik')
        ;
        $this->registerControl($iik_component);

        $kbe_component = (new Input())
            ->setPlaceholder('Код бенефициара')
            ->setName('kbe')
        ;
        $this->registerControl($kbe_component);

        $knp_component = (new Input())
            ->setPlaceholder('Код назначения платежа')
            ->setName('knp')
        ;
        $this->registerControl($knp_component);

        $button = (new Button())
            ->setName('submit')
            ->setContent('Сохранить')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
        ;
        $this->registerControl($button);
    }

    /**
     * @param SearchSelect $select
     * @return SearchSelect
     */
    private function setProfile(SearchSelect $select) {
        /** @var Firm $model */
        $model = $this->getModel();
        if (!$model->isNewRecord) {
            $profile_id = $model->profile_id;
            if ($profile_id) {
                /** @var Profile $profile */
                $profile = Profile::findOne($profile_id);
                $select->addOption(new Option($profile->id, $profile->title));
            }
        }
        return $select;
    }

    /**
     * @param SearchSelect $select
     * @return SearchSelect
     */
    private function setUser(SearchSelect $select) {
        /** @var Firm $model */
        $model = $this->getModel();
        if (!$model->isNewRecord) {
            $user_id = $model->user_id;
            if ($user_id) {
                /** @var User $user */
                $user = User::findOne($user_id);
                $select->addOption(new Option($user->id, $user->email));
            }
        }
        return $select;
    }

    /**
     * @param Select $select
     * @return $this
     */
    private function populateCountries(Select $select) {

        $country_map = (new Location())->getPossibleCountries();
        $country_map = array_flip($country_map);

        foreach ($country_map as $country_id => $name) {
            $select->addOption(new Option($country_id, $name));
        }
        return $this;
    }

    /**
     * Пополнение категорий.
     * @param Select $select
     * @return Select
     */
    private function populateCategories(Select $select) {
        /** @var Firm $model */
        $model = $this->getModel();
        /** @var Category[] $category_list */
        $category_list = Category::find()->where(['parent' => 0])->all();

        // выбранные категории.
        $selected_categories = [];
        if (!$model->isNewRecord) {
            $selected_categories = $model->getCategoryIds();
        }
        $select->setValue($selected_categories);

        foreach ($category_list as $category) {
            $option = new Option($category->id, $category->title);
            $select->addOption($option);
        }
        return $select;
    }

    /**
     * @param Select $select
     * @return Select
     */
    private function populateRegions(Select $select) {

        $region_map = (new Location())->getPossibleRegions();
        $region_map = array_flip($region_map);

        foreach ($region_map as $region_id => $name) {
            $select->addOption(new Option($region_id, $name));
        }
        return $select;
    }

    /**
     * @param Select $select
     * @return Select
     */
    private function populateCities(Select $select) {
        /** @var Location[] $locations */
        $locations = Location::find()->all();
        $select->addOption(new Option(0, 'Не выбран'));
        foreach ($locations as $location) {
            $select->addOption(new Option($location->id, $location->title));
        }
        return $select;
    }

    /**
     * @return array
     */
    public function validate(): array {
        /** @var Firm $model */
        $model = $this->getModel();
        $this->populateModel();
        $model->validate();
        $this->populateErrorsFromAR($model->getErrors());
        return parent::validate();
    }

    protected function populateModel(): void {

        /** @var Firm $model */
        $model = $this->getModel();
        $form_data = $this->getFormData();

        foreach ($form_data as $attribute_name => $value) {
            if ($attribute_name == 'image') {
                if (is_array($value)) {
                    $model->setImageForUpload($value);
                }
                continue;
            }
            if ($attribute_name == 'categories') {
                $model->setCategories((array)$value);
            }
            else {
                if ($model->hasAttribute($attribute_name)) {
                    $model->setAttribute($attribute_name, $value);
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function save(): bool {
        /** @var Firm $model */
        $model = $this->getModel();
        return $model->save();
    }
}