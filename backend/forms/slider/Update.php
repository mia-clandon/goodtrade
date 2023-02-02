<?php

namespace backend\forms\slider;

use yii\helpers\Url;

use common\libs\form\components\Option;
use common\models\firms\Firm;
use common\models\MainSlider;
use common\libs\form\validators\client\Required as RequiredClient;

use backend\components\form\controls\Selectize;
use backend\components\form\controls\Image;
use backend\components\form\controls\Button;
use backend\components\form\controls\Input;
use backend\components\form\controls\Select;
use backend\components\form\controls\TextArea;
use backend\components\form\Form;

/**
 * Class Update
 * @package backend\forms\main
 * @author yerganat
 */
class Update extends Form {
    private $is_slide = false;

    public function setIsSlide() {
        $this->is_slide = true;
        return $this;
    }

    public function initControls(): void {
        parent::initControls();

        /** @var MainSlider $slider_model */
        $slider_model = $this->getModel();

        $slider_id_control = (new Input())
            ->setType(Input::TYPE_HIDDEN)
            ->setValue(!$slider_model->isNewRecord ? $slider_model->id : 0)
            ->setName('slider_id');
        $this->registerControl($slider_id_control);

        $slide_id_control = (new Input())
            ->setType(Input::TYPE_HIDDEN)
            ->setValue($slider_model->slide_id)
            ->setName('slide_id');
        $this->registerControl($slide_id_control);

        if(!$this->is_slide) {
            $firm_control = (new Selectize())
                ->setName('firm_id')
                ->setTitle('Компания')
                ->setPlaceholder('Выберите компанию');
            $this->populateFirm($firm_control);
            $this->registerControl($firm_control);
        }

        $title_input = (new Input())
            ->setName('title')
            ->setPlaceholder('Заголовок')
            ->setJsValidator([(new RequiredClient())])
        ;
        $this->registerControl($title_input);

        $text_component = (new TextArea())
            ->setLoadCKEditor()
            ->setPlaceholder('Описание')
            ->setName('description')
            ->addAttribute('rows', 3)
        ;
        $this->registerControl($text_component);

        $image_control = (new Image())
            ->setTitle('Изображение')
            ->setName('image')
            ->setSizes(200, null, 'AUTO')
        ;
        $this->populateImages($image_control);
        $this->registerControl($image_control);


        $tip_input = (new Input())
            ->setName('button')
            ->setPlaceholder('Текст кнопки')
            ->setJsValidator([(new RequiredClient())])
        ;
        $this->registerControl($tip_input);

        $link_input = (new Input())
            ->setName('link')
            ->setPlaceholder('Ссылка')
            ->setJsValidator([(new RequiredClient())])
        ;
        $this->registerControl($link_input);

        $tip_input = (new Input())
            ->setName('tip')
            ->setPlaceholder('Комментарии к кнопке')
        ;
        $this->registerControl($tip_input);

        $button = (new Button())
            ->setName('submit')
            ->setContent('Сохранить')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
        ;
        $this->registerControl($button);


        $add_control = (new Button())
            ->setName('back')
            ->setContent('Отменить')
            ->setType(Button::TYPE_BUTTON)
            ->setButtonType(Button::BTN_TYPE_LINK)
            ->setRedirectOnClick(Url::to(['slider/index']))
        ;
        $this->registerControl($add_control);
    }

    /**
     * Пополнение компании.
     * @param Select $select
     * @return Select
     */
    private function populateFirm(Select $select) {
        /** @var Firm[] $firm_list */
        $firm_list = Firm::find()->where(['status' => 1])->all();

        $select->setValue([$this->getModel()->firm_id]);

        $option = new Option(0, 'Нет');
        $select->addOption($option);

        foreach ($firm_list as $firm) {
            $option = new Option($firm->id, $firm->title);
            $select->addOption($option);
        }
        return $select;
    }

    /**
     * @param Image $image_control
     * @return Image
     */
    private function populateImages(Image $image_control) {
        /** @var MainSlider $model */
        $model = $this->getModel();
        if (!$model->isNewRecord) {
            if($model->image) {
                $image_control->setValue([$model->image]);
            }
        }
        return $image_control;
    }

    /**
     * @return array
     */
    public function validate(): array {
        /** @var MainSlider $model */
        $model = $this->getModel();
        $this->populateModel();
        $model->validate();
        $this->populateErrorsFromAR($model->getErrors());
        return parent::validate();
    }

    protected function populateModel(): void {

        /** @var MainSlider $model */
        $model = $this->getModel();
        $form_data = $this->getFormData();

        foreach ($form_data as $attribute_name => $value) {
            switch ($attribute_name) {
                case "image": {
                    $model->setImage(!is_array($value) ? [$value] : $value);
                } break;
                default: {
                    if ($model->hasAttribute($attribute_name)) {
                        $model->setAttribute($attribute_name, $value);
                    }
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function save(): bool  {
        /** @var MainSlider $model */
        $model = $this->getModel();
        $saved = $model->save();
        if ($saved === false) {
            // beforeSave может сгенерировать ошибку сохранения.
            $this->populateErrorsFromAR($model->getErrors());
        }
        return $saved;
    }
}