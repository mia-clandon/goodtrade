<?php

namespace frontend\components\form\controls;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use common\libs\form\components\Input as BaseInput;
use common\modules\image\helpers\Image as ImageHelper;

/**
 * Class Image
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Image extends BaseInput {

    const FOR_REMOVE_KEY = 'for_remove';

    /** @var bool */
    private $multiple = false;

    /** @var array - массив с фото установленными в контрол. */
    private $images = [];

    /** @var array - размеры миниатюры при отображении. */
    private $thumbnail_sizes = ['width' => 50, 'height' => 50, 'type' => ImageHelper::RESIZE_MODE_AUTO];

    protected function beforeRender() {
        parent::beforeRender();
        $this->setType(static::TYPE_FILE);
        $this->addClass('input-photo-field');
        if ($this->isMultiple()) {
            $this->addAttribute('multiple', 'multiple');
        }
    }

    /**
     * Контрол может принимать массив с путями к фото, либо 1 фото (строку).
     * @param array|string $value
     * @return $this
     */
    public function setValue($value) {
        if (is_array($value)) {
            $this->images = $value;
        }
        else if (null !== $value) {
            $this->images = [$value];
        }
        parent::setValue(Json::encode($this->images));
        return $this;
    }

    /**
     * Возвращает массив с фото.
     * @return array
     */
    public function getValue() {
        return $this->images;
    }

    /**
     * Возвращает массив с уменьшенными фото.
     * @return array
     */
    private function getImages(): array {

        $images = [];
        foreach ($this->images as $image) {

            // размеры миниатюры.
            $w      = (int)ArrayHelper::getValue($this->thumbnail_sizes, 'width');
            $h      = (int)ArrayHelper::getValue($this->thumbnail_sizes, 'height');
            $type   = (int)ArrayHelper::getValue($this->thumbnail_sizes, 'type');

            $images[] = [
                'thumbnail' => ImageHelper::i()->generateRelativeImageLink($image, $w, $h, $type),
                'original' => $image,
            ];
        }
        return $images;
    }

    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control'       => $control,
            'control_id'    => 'image_control_'.$this->getName(),
            'images'        => $this->getImages(),
            'has_images'    => (bool)count($this->images),
            'is_multiple'   => $this->isMultiple(),
        ]);
    }

    /**
     * @return boolean
     */
    public function isMultiple(): bool {
        return (bool)$this->multiple;
    }

    /**
     * @param boolean $multiple
     * @return $this
     */
    public function setMultiple(bool $multiple = true) {
        $this->multiple = (bool)$multiple;
        return $this;
    }
}