<?php

namespace backend\components\form\controls;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

use common\modules\image\helpers\Image as ImageHelper;
use common\libs\form\components\Image as BaseImage;
use common\libs\traits\RegisterJsScript;

use frontend\assets\UploadAsset;

/**
 * Class Input
 * TODO: переделать компонент по аналогии \frontend\components\form\controls\Image
 * @package backend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Image extends BaseImage {
    use RegisterJsScript;

    /** @var string */
    private $remove_action;

    /** @var array */
    private $additional_params = [];

    /** @var bool */
    private $enable_sorting = false;

    /** @var array  */
    private $sizes = ['width' => 200, 'type' => ImageHelper::RESIZE_MODE_AUTO];

    protected function registerFormAssets() {
        // плагин загрузчика
        UploadAsset::register(\Yii::$app->getView());
    }

    protected function beforeRender() {
        parent::beforeRender();
        $this->registerJsScript();
        $this->addDataAttribute('url', '/api/uploader/upload');
        if ($this->isMultiple()) {
            $this->addAttribute('multiple', 'multiple');
        }
    }

    /**
     * @return string
     */
    protected function getFileControl() {
        return Html::input('file', $this->getName(), null, $this->getAttributes());
    }

    /**
     * Дополнительные параметры для атрибутов фото.
     * todo: лучше сделать через setAttributes()
     * @param array $params
     * @return $this
     */
    public function setAdditionalParams(array $params) {
        $this->additional_params = $params;
        return $this;
    }

    public function render(): string {
        parent::render();

        $values = $this->getValue();
        $values = (!is_array($values)) ? [$values] : $values;
        $values = array_filter($values);

        $control = $this->getFileControl();

        $image_id = 0; // идентификатор фото.
        if ($this->getDataAttribute('image-id')) {
            $image_id = $this->getDataAttribute('image-id');
        }

        $images = [];
        foreach ($values as $id => $value) {
            $key = ($image_id) ? $image_id : $id; // идентификатор фото

            $width = ArrayHelper::getValue($this->sizes, 'width');
            $height = ArrayHelper::getValue($this->sizes, 'height');
            $type = (int)ArrayHelper::getValue($this->sizes, 'type');

            $images[$key] = ImageHelper::i()->generateRelativeImageLink($value, $width, $height, $type ? strtolower($type) : null);
        }

        $not_multiple_name = str_replace('[]', '', $this->getName());

        return $this->renderTemplate([
            'control'               => $control,
            'images'                => $images,
            'remove_action'         => $this->getRemoveAction(),
            'component_name'        => $not_multiple_name,
            'additional_params'     => $this->additional_params,
            'image_sorting_class'   => $this->enable_sorting ? 'image-sorting' : '',
            'settings'              => Json::encode([
                'component_name' => $not_multiple_name,
                'is_multiple' => $this->isMultiple(),
                'tmp_sizes' => $this->getSizes(),
            ])
        ]);
    }

    /**
     * @param $action
     * @return $this
     */
    public function setRemoveAction($action) {
        $this->remove_action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getRemoveAction() {
        return $this->remove_action;
    }

    /**
     * @return bool
     */
    public function getEnableSorting() {
        return $this->enable_sorting;
    }

    /**
     * @param bool $sorting
     * @return $this
     */
    public function setEnableSorting($sorting = true) {
        $this->enable_sorting = $sorting;
        return $this;
    }

    /**
     * @param int $w
     * @param int|null $h
     * @param int $type
     * @return $this
     */
    public function setSizes($w = 200, $h = null, $type = ImageHelper::RESIZE_MODE_AUTO) {
        if ($w) {
            $this->sizes['width'] = $w;
            if ($h) {
                $this->sizes['height'] = $h;
            }
            if ($type) {
                $this->sizes['type'] = $type;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getSizes() {
        return $this->sizes;
    }

    /**
     * @return $this
     */
    public function clearSizes() {
        $this->sizes = [];
        return $this;
    }
}