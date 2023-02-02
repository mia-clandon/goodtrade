<?php

namespace backend\components\form\traits;

use backend\components\form\controls\Bootstrap;

/**
 * Trait SizeBootstrapTrait
 * @package backend\components\form\traits
 * @author Артём Широких kowapssupport@gmail.com
 */
trait SizeBootstrapTrait {

    /** @var null|string */
    private $control_col_width;

    /**
     * @see \backend\components\form\controls\Bootstrap
     * @param string $width
     * @return $this
     */
    public function setControlColWidth($width) {
        $possible_sizes = Bootstrap::getSizes();
        if (!in_array($width, $possible_sizes)) {
            return $this;
        }
        $this->control_col_width = (string)$width;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getControlColWidth() {
        return $this->control_col_width;
    }
}