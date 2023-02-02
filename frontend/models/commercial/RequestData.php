<?php

namespace frontend\models\commercial;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use common\libs\StringHelper;
use frontend\interfaces\ICommercialRequestData;

/**
 * Class RequestData
 * @package frontend\models\commercial
 * @author Артём Широких kowapssupport@gmail.com
 */
class RequestData implements ICommercialRequestData {

    const KEY_IS_ALL_MODIFICATIONS = 'is_all_modifications';
    const KEY_TERM_IDS = 'term_ids';

    /** @var bool */
    private $is_all_modifications = false;

    /** @var array */
    private $terms = [];

    public function setIsAllModifications($flag = true) {
        $this->is_all_modifications = (bool)$flag;
        return $this;
    }

    public function isAllModifications() {
        return $this->is_all_modifications;
    }

    public function setTerms(array $terms) {
        $terms = array_map('intval', $terms);
        $terms = array_filter($terms, function($value) {
            return ($value > 0);
        });
        $this->terms = $terms;
        return $this;
    }

    public function getTerms() {
        return $this->terms;
    }

    public function getData() {
        return Json::encode([
            self::KEY_IS_ALL_MODIFICATIONS => $this->isAllModifications(),
            self::KEY_TERM_IDS => $this->getTerms(),
        ]);
    }

    public function setData($json) {
        if (!StringHelper::isJson($json)) {
            throw new Exception('Передаваемые данные не являются JSON объектом.');
        }
        $data = Json::decode($json);
        //load is_all_modifications
        $is_all_modifications = ArrayHelper::getValue($data, self::KEY_IS_ALL_MODIFICATIONS);
        if (!is_null($is_all_modifications)) {
            $this->setIsAllModifications($is_all_modifications);
        }
        //load terms
        $terms = ArrayHelper::getValue($data, self::KEY_TERM_IDS);
        if (!is_null($terms)) {
            $this->setTerms($terms);
        }
        return $this;
    }
}