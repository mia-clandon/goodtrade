<?php

namespace frontend\models\commercial;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use common\models\goods\Product;
use common\libs\StringHelper;

use frontend\interfaces\ICommercialResponseData;

/**
 * Class ResponseData
 * @package frontend\models\commercial
 * @author Артём Широких kowapssupport@gmail.com
 */
class ResponseData implements ICommercialResponseData {

    const KEY_COMPANY_CITY_ID   = 1;
    const KEY_COMPANY_ADDRESS   = 2;
    const KEY_COMPANY_EMAILS    = 3;
    const KEY_COMPANY_PHONES    = 4;
    const KEY_DELIVERY_TERMS    = 5;
    const KEY_COMPANY_BIN       = 6;
    const KEY_COMPANY_BANK      = 7;
    const KEY_COMPANY_BIK       = 8;
    const KEY_COMPANY_IIK       = 9;
    const KEY_COMPANY_KBE       = 10;
    const KEY_COMPANY_KNP       = 11;

    private $params = [];

    public function setCompanyCity($city_id) {
        $this->params[self::KEY_COMPANY_CITY_ID] = (int)$city_id;
        return $this;
    }

    public function getCompanyCity() {
        return (isset($this->params[self::KEY_COMPANY_CITY_ID]))
            ? $this->params[self::KEY_COMPANY_CITY_ID]
            : null
        ;
    }

    public function setCompanyAddress($address) {
        $this->params[self::KEY_COMPANY_ADDRESS] = (string)$address;
        return $this;
    }

    public function getCompanyAddress() {
        return (isset($this->params[self::KEY_COMPANY_ADDRESS]))
            ? $this->params[self::KEY_COMPANY_ADDRESS]
            : null
        ;
    }

    public function setCompanyEmails(array $emails) {
        $emails = array_filter($emails, function($email) {
            return (!empty($email));
        });
        $this->params[self::KEY_COMPANY_EMAILS] = $emails;
        return $this;
    }

    public function getCompanyEmails() {
        return (isset($this->params[self::KEY_COMPANY_EMAILS]))
            ? $this->params[self::KEY_COMPANY_EMAILS]
            : []
        ;
    }

    public function setCompanyPhones(array $phones) {
        $phones = array_filter($phones, function($phone) {
            return (!empty($phone));
        });
        $phones = array_map(function ($phone) {
            return (string)preg_replace('/[^0-9]/', '', $phone);
        }, $phones);
        $this->params[self::KEY_COMPANY_PHONES] = $phones;
        return $this;
    }

    public function getCompanyPhones() {
        return (isset($this->params[self::KEY_COMPANY_PHONES]))
            ? $this->params[self::KEY_COMPANY_PHONES]
            : []
        ;
    }

    public function setDeliveryTerms(array $delivery_terms) {
        $delivery_terms_id = [];
        $product_model = new Product();
        $all_delivery_terms = array_flip($product_model->getDeliveryTermsHelper()->getAllDeliveryTerms());
        foreach ($delivery_terms as $delivery_term_key) {
            if (array_key_exists($delivery_term_key, $all_delivery_terms)) {
                $delivery_terms_id[] = $all_delivery_terms[$delivery_term_key];
            }
        }
        $this->params[self::KEY_DELIVERY_TERMS] = $delivery_terms_id;
        return $this;
    }

    public function getDeliveryTerms() {
        return (isset($this->params[self::KEY_DELIVERY_TERMS]))
            ? array_unique($this->params[self::KEY_DELIVERY_TERMS])
            : []
        ;
    }

    public function setCompanyBin($bin) {
        $this->params[self::KEY_COMPANY_BIN] = (string)$bin;
        return $this;
    }

    public function getCompanyBin() {
        return (isset($this->params[self::KEY_COMPANY_BIN]))
            ? $this->params[self::KEY_COMPANY_BIN]
            : null
        ;
    }

    public function setCompanyBank($bank) {
        $this->params[self::KEY_COMPANY_BANK] = (string)$bank;
        return $this;
    }

    public function getCompanyBank() {
        return (isset($this->params[self::KEY_COMPANY_BANK]))
            ? $this->params[self::KEY_COMPANY_BANK]
            : null
        ;
    }

    public function setCompanyBik($bik) {
        $this->params[self::KEY_COMPANY_BIK] = (string)$bik;
        return $this;
    }

    public function getCompanyBik() {
        return (isset($this->params[self::KEY_COMPANY_BIK]))
            ? $this->params[self::KEY_COMPANY_BIK]
            : null
        ;
    }

    public function setCompanyIik($iik) {
        $this->params[self::KEY_COMPANY_IIK] = (string)$iik;
        return $this;
    }

    public function getCompanyIik() {
        return (isset($this->params[self::KEY_COMPANY_IIK]))
            ? $this->params[self::KEY_COMPANY_IIK]
            : null
        ;
    }

    public function setCompanyKbe($kbe) {
        $this->params[self::KEY_COMPANY_KBE] = (string)$kbe;
        return $this;
    }

    public function getCompanyKbe() {
        return (isset($this->params[self::KEY_COMPANY_KBE]))
            ? $this->params[self::KEY_COMPANY_KBE]
            : null
        ;
    }

    public function setCompanyKnp($knp) {
        $this->params[self::KEY_COMPANY_KNP] = (string)$knp;
        return $this;
    }

    public function getCompanyKnp() {
        return (isset($this->params[self::KEY_COMPANY_KNP]))
            ? $this->params[self::KEY_COMPANY_KNP]
            : null
        ;
    }

    public function getData() {
        return Json::encode([
            self::KEY_COMPANY_CITY_ID   => $this->getCompanyCity(),
            self::KEY_COMPANY_ADDRESS   => $this->getCompanyAddress(),
            self::KEY_COMPANY_EMAILS    => $this->getCompanyEmails(),
            self::KEY_COMPANY_PHONES    => $this->getCompanyPhones(),
            self::KEY_DELIVERY_TERMS    => $this->getDeliveryTerms(),
            self::KEY_COMPANY_BIN       => $this->getCompanyBik(),
            self::KEY_COMPANY_BANK      => $this->getCompanyBank(),
            self::KEY_COMPANY_BIK       => $this->getCompanyBik(),
            self::KEY_COMPANY_IIK       => $this->getCompanyIik(),
            self::KEY_COMPANY_KBE       => $this->getCompanyKbe(),
            self::KEY_COMPANY_KNP       => $this->getCompanyKnp(),
        ]);
    }

    public function setData($json) {
        if (!StringHelper::isJson($json)) {
            throw new Exception('Передаваемые данные не являются JSON объектом.');
        }
        $data = Json::decode($json);
        $company_city_id = ArrayHelper::getValue($data, self::KEY_COMPANY_CITY_ID, null);
        if (!is_null($company_city_id)) {
            $this->params[self::KEY_COMPANY_CITY_ID] = $company_city_id;
        }
        $company_address = ArrayHelper::getValue($data, self::KEY_COMPANY_ADDRESS, null);
        if (!is_null($company_address)) {
            $this->params[self::KEY_COMPANY_ADDRESS] = $company_address;
        }
        $company_emails = ArrayHelper::getValue($data, self::KEY_COMPANY_EMAILS, []);
        if (!empty($company_emails)) {
            $this->params[self::KEY_COMPANY_EMAILS] = $company_emails;
        }
        $company_phones = ArrayHelper::getValue($data, self::KEY_COMPANY_PHONES, []);
        if (!empty($company_phones)) {
            $this->params[self::KEY_COMPANY_PHONES] = $company_phones;
        }
        $delivery_terms = ArrayHelper::getValue($data, self::KEY_DELIVERY_TERMS, []);
        if (!empty($delivery_terms)) {
            $this->params[self::KEY_DELIVERY_TERMS] = $delivery_terms;
        }
        $company_bin = ArrayHelper::getValue($data, self::KEY_COMPANY_BIN, null);
        if (!is_null($company_bin)) {
            $this->params[self::KEY_COMPANY_BIN] = $company_bin;
        }
        $company_bank = ArrayHelper::getValue($data, self::KEY_COMPANY_BANK, null);
        if (!is_null($company_bank)) {
            $this->params[self::KEY_COMPANY_BANK] = $company_bank;
        }
        $company_bik = ArrayHelper::getValue($data, self::KEY_COMPANY_BIK, null);
        if (!is_null($company_bik)) {
            $this->params[self::KEY_COMPANY_BIK] = $company_bik;
        }
        $company_iik = ArrayHelper::getValue($data, self::KEY_COMPANY_IIK, null);
        if (!is_null($company_iik)) {
            $this->params[self::KEY_COMPANY_IIK] = $company_iik;
        }
        $company_kbe = ArrayHelper::getValue($data, self::KEY_COMPANY_KBE, null);
        if (!is_null($company_kbe)) {
            $this->params[self::KEY_COMPANY_KBE] = $company_kbe;
        }
        $company_knp = ArrayHelper::getValue($data, self::KEY_COMPANY_KNP, null);
        if (!is_null($company_knp)) {
            $this->params[self::KEY_COMPANY_KNP] = $company_knp;
        }
        return $this;
    }
}