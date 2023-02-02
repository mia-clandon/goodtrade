<?php

namespace frontend\modules\cabinet\forms\product;

use yii\web\UploadedFile;

use common\models\firms\Firm;
use common\libs\form\Form;
use common\models\PriceQueue;

use frontend\components\form\controls\StandardFile;
use frontend\components\form\controls\Input;

/**
 * Class AddPrice
 * @package frontend\modules\cabinet\forms\product
 * @author Артём Широких kowapssupport@gmail.com
 */
class AddPrice extends Form {

    /** @var StandardFile|null */
    private $files_control;

    protected function initControls(): void {
        parent::initControls();

        //todo
        $this->addAttribute('enctype', 'multipart/form-data');
        $this->addClass('cabinet-product-add-price-form');

        /*
         * todo: раньше загрузка работала через ajax.
        $file_control = (new File())
            ->setName('price_file')
            // разрешенные форматы для загрузки (js).
            ->addAllowedFileFormats(['xls', 'xlsx', 'csv'])
        ;
        $this->registerControl($file_control);
        */

        $this->registerControl($this->getFileControl());

        $submit = (new Input())
            ->setType(Input::TYPE_SUBMIT)
            ->setName('submit')
            ->setDisplayNone()
        ;
        $this->registerControl($submit);
    }

    /**
     * @return StandardFile
     */
    private function getFileControl(): StandardFile {
        if (null === $this->files_control) {
            $this->files_control = (new StandardFile())
                ->setLabelTip('Выберите файл для загрузки прайс листа.')
                ->setName('price_file');
        }
        return $this->files_control;
    }

    protected function populateModel(): void {
        /** @var PriceQueue $model */
        $model = $this->getModel();
        $form_data = $this->getFormData();
        foreach ($form_data as $attribute_name => $value) {
            if ($model->hasAttribute($attribute_name)) {
                $model->setAttribute($attribute_name, $value);
            }
        }
        $model->firm_id = Firm::get()->id;

        //todo: в form-data пока не приходит файл, поправить в будущем.
        $model->price_file = UploadedFile::getInstanceByName($this->getFileControl()->getName());
        $this->setModel($model);
    }

    public function validate(): array {
        $this->populateModel();
        $this->getModel()->validate();

        $uploaded = UploadedFile::getInstanceByName($this->getFileControl()->getName());
        if (empty($uploaded)) {
            $this->addError($this->getFileControl()->getName(), 'Выберите файл для загрузки.');
        }

        $this->populateErrorsFromAR($this->getModel()->getErrors());
        return parent::validate();
    }

    public function save(): bool {
        /** @var PriceQueue $model */
        $model = $this->getModel();
        $is_uploaded = $model->upload();
        if ($is_uploaded) {
            return $model->save();
        }
        else {
            $this->addError('price_file', 'Ошибка загрузки файла.');
            return false;
        }
    }
}