<?php

namespace frontend\forms\commercial;

use common\libs\form\Form;

/**
 * Class Base
 * @package frontend\forms\commercial
 */
class Base extends Form {

    /** @var int  */
    protected $firm_id = 0;
    /** @var int  */
    protected $product_id = 0;

    /**
     * @return int
     */
    public function getFirmId() {
        return $this->firm_id;
    }

    /**
     * @param int $firm_id
     * @return $this;
     */
    public function setFirmId($firm_id) {
        $this->firm_id = $firm_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductId() {
        return $this->product_id;
    }

    /**
     * @param int $product_id
     * @return $this;
     */
    public function setProductId($product_id) {
        $this->product_id = $product_id;
        return $this;
    }

    /**
     * @param array $product_id
     * @return $this
     */
    public function setProductIdList(array $product_id) {
        //todo:
        return $this;
    }

}