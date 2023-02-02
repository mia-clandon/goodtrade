<?php

namespace common\libs\form;

use yii\base\DynamicModel as BaseDynamicModel;

/**
 * Class DynamicModel
 * @package common\libs\form
 * @author Артём Широких kowapssupport@gmail.com
 */
class DynamicModel extends BaseDynamicModel {

    /** @var array  */
    private $labels = [];

    /**
     * @param array $labels
     * @return $this
     */
    public function setLabels(array $labels) {
        $this->labels = array_filter($labels, function($value) { return ($value); });
        return $this;
    }

    public function attributeLabels() {
        return $this->labels;
    }
}