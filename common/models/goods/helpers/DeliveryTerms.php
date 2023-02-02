<?php

namespace common\models\goods\helpers;

use common\models\goods\DeliveryTerms as DeliveryTermModel;
use yii\helpers\ArrayHelper;

/**
 * Class DeliveryTerms
 * @package common\models\goods\helpers
 * @author Артём Широких kowapssupport@gmail.com
 */
class DeliveryTerms extends Base {

    /** @var array Условия доставки */
    private $delivery_terms_allowed = [
        1 => 'EXW',
        2 => 'FCA',
        3 => 'FAS',
        4 => 'FOB',
        5 => 'CFR',
        6 => 'CIF',
        7 => 'CPT',
        8 => 'CIP',
        9 => 'DAT',
        10 => 'DAP',
        11 => 'DDP',
    ];

    /** @var array Условия доставки. */
    private $delivery_terms = NULL;

    /**
     * Устанавливает условия доставки в модель.
     * @param array $delivery_terms
     * @return $this
     */
    public function setDeliveryTerms(array $delivery_terms) {
        if (empty($delivery_terms)) {
            $this->delivery_terms = [];
        }
        $this->delivery_terms = $delivery_terms;
        return $this;
    }

    /**
     * Возвращает строковое название условия доставки.
     * @param int $term_id
     * @return string
     */
    public function getDeliveryTermText($term_id) {
        if (isset($this->delivery_terms_allowed[$term_id])) {
            return $this->delivery_terms_allowed[$term_id];
        }
        return '';
    }

    /**
     * Возвращает условия доставки товара.
     * @return array
     */
    public function getDeliveryTerms() {
        $delivery_terms = $this->getDeliveryTermsIds();
        $out_result = [];
        foreach ($delivery_terms as $id) {
            $out_result[$id] = $this->getDeliveryTermText($id);
        }
        return $out_result;
    }

    /**
     * Возвращает условия доставки, разделённые через запятую.
     * Последний тип доставки соединён через " и ".
     * TODO: есть сомнения в корректности работы метода.
     * @var array|null $delivery_term_ids
     * @return string
     */
    public function getDeliveryTermsString($delivery_term_ids = null) {
        if(null === $delivery_term_ids) {
            $delivery_terms = $this->getDeliveryTerms();
        } else {
            $delivery_terms = [];
            foreach ($delivery_term_ids as $id) {
                $delivery_terms[$id] = $this->getDeliveryTermText($id);
            }
        }

        $delivery_terms_string = implode(", ", $delivery_terms);
        if(mb_strlen($delivery_terms_string) > 5) {
            $delivery_terms_string = substr_replace($delivery_terms_string, " и ", -5, 2);
        }

        return $delivery_terms_string;
    }

    /**
     * Обновление условий доставки товара.
     * @param boolean $remove_not_exist
     * @return array
     */
    public function updateDeliveryTerms($remove_not_exist = true) {
        $delivery_terms = (array)$this->delivery_terms;
        $product_id = $this->getProductModel()->id;
        if ($remove_not_exist && !is_null($this->delivery_terms)) {
            // удаляю связи которые есть в базе но нет в $items;
            $current_product_delivery_terms = DeliveryTermModel::find()->where(['product_id' => $product_id])
                ->select(['delivery_term_id'])
                ->asArray()
                ->all();
            $current_product_delivery_terms = ArrayHelper::getColumn($current_product_delivery_terms, 'delivery_term_id');
            $for_remove_relations = array_diff($current_product_delivery_terms, $delivery_terms);
            DeliveryTermModel::deleteAll(['product_id' => $product_id, 'delivery_term_id' => $for_remove_relations]);
        }
        $errors = [];
        foreach ($delivery_terms as $term_id) {
            $delivery_term = new DeliveryTermModel();
            $delivery_term->delivery_term_id = $term_id;
            $delivery_term->product_id = $product_id;
            $result = $delivery_term->save();
            if (!$result) {
                $errors = ArrayHelper::merge($delivery_term->getErrors(), $errors);
            }
        }
        return $errors;
    }

    /**
     * Возвращает все условия доставки.
     * @return array
     */
    public function getAllDeliveryTerms() {
        return $this->delivery_terms_allowed;
    }

    /**
     * Возвращает идентификаторы условий доставки товара.
     * @return array
     */
    public function getDeliveryTermsIds() {
        $terms = DeliveryTermModel::find()
            ->select('delivery_term_id')
            ->where(['product_id' => $this->getProductModel()->id])
            ->asArray()
            ->all();
        $terms = ArrayHelper::getColumn($terms, 'delivery_term_id', []);
        return $terms;
    }
}