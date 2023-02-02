<?php

namespace frontend\components\lib;

use yii\base\Exception;

use common\models\commercial\Response;
use common\libs\RegionHelper;
use common\libs\traits\Singleton;
use common\models\firms\Email;
use common\models\firms\Firm;
use common\models\firms\Phone;
use common\models\goods\Product;
use common\models\Location;
use common\models\commercial\Request;
use common\models\goods\helpers\DeliveryTerms;

/**
 * Class CommercialResponseProcessor
 * @package frontend\components\lib
 * @author Артём Широких kowapssupport@gmail.com
 */
class CommercialResponseProcessor {
    use Singleton;

    /** @var  Product */
    private $product;
    /** @var  Request */
    private $request;
    /** @var  Response */
    private $response;

    /**
     * Использовать ли данные с организации владельца коммерческого предложения.
     * @var bool
     */
    private $use_response_firm_owner = false;

    /**
     * Данные для коммерческого предложения.
     * @return array
     */
    public function getCommerceSettings() {
        $commerce_settings_data = array_merge(
            $this->getContactInfoPart(),
            $this->getRequestInfoPart(),
            $this->getProductInfoPart(),
            $this->getRequisitesPart(),
            $this->getLocationPart()
        );
        return $commerce_settings_data;
    }

    /**
     * Возвращает контактные данные организации.
     * @return array
     */
    private function getContactInfoPart() {
        $firm = $this->getFirm();
        $emails = [];
        /** @var Email $email */
        foreach ($firm->getEmails()->all() as $email) {
            $emails[] = $email->email;
        }
        $phones = [];
        /** @var Phone $phone */
        foreach ($firm->getPhones()->all() as $phone) {
            $phones[] = $phone->getInternationalFormatPhone();
        }
        return [
            'emails' => $emails,
            'tels' => $phones,
        ];
    }

    /**
     * Возвращает данные по коммерческому запросу.
     * @return array
     */
    private function getRequestInfoPart() {
        $response_model = new Response();
        $request = $this->getRequest();
        $response = $this->getResponse();
        return [
            // срок действия коммерческого предложения.
            'commerceTime' => $response_model->getResponseValidity(),
            // в наличии - todo пока всегда.
            'inStock' => is_null($response)?true:$response->in_stock,
            // срок до отправки  - todo пока всегда 0.
            'deliveryTime' => is_null($response)?0:$response->time_to_send,
            // количество товара. (из коммерческого запроса)
            'productCount' => is_null($response)?$request->part_size:$response->product_count,
        ];
    }

    /**
     * Возвращает данные по товару.
     * @return array
     */
    private function getProductInfoPart() {
        $product = $this->getProduct();
        $response = $this->getResponse();

        $delivery_terms = [];
        if(!is_null($response)) {
            $delivery_term = new DeliveryTerms();
            $delivery_term_ids = $response->getResponseData()->getDeliveryTerms();
            foreach ($delivery_term_ids as $term_id) {
                $delivery_terms[] = $delivery_term->getDeliveryTermText($term_id);
            }
        }
        return [
            // вывод краткой единицы измерения.
            'productUnitShort' => $product->getUnitText(null, true).'.',
            'productUnitLong' => 'за '.$product->getUnitText(null, true),
            // цена за единицу товара.
            'productPrice' => is_null($response)?$product->getPrice():$response->product_price,
            'withVat' => is_null($response)?$product->price_with_vat:$response->with_vat,
            'prepay' => is_null($response)?70:$response->pre_payment,
            'postpay' => is_null($response)?30:$response->post_payment,
            // условия доставки.
            'deliveryConditions' => is_null($response)?array_values($product->getDeliveryTermsHelper()->getDeliveryTerms()):$delivery_terms,
        ];
    }

    /**
     * Возвращает данные по реквизитам.
     * @return array
     */
    private function getRequisitesPart() {
        $requisites = [];
        $response = $this->getResponse();
        if(null === $response) {
            $firm = $this->getFirm();
            foreach (['bin', 'bank', 'bik', 'iik', 'kbe', 'knp'] as $requisite) {
                if ($firm->hasAttribute($requisite)) {
                    $requisites[$requisite] = $firm->{$requisite};
                }
            }
        } else {
            $extra_data = $response->getResponseData();
            foreach (['bin', 'bank', 'bik', 'iik', 'kbe', 'knp'] as $requisite) {
                switch ($requisite) {
                    case 'bin':
                        $requisites[$requisite] = $extra_data->getCompanyBin();
                        break;
                    case 'bank':
                        $requisites[$requisite] = $extra_data->getCompanyBank();
                        break;
                    case 'bik':
                        $requisites[$requisite] = $extra_data->getCompanyBik();
                        break;
                    case 'iik':
                        $requisites[$requisite] = $extra_data->getCompanyIik();
                        break;
                    case 'kbe':
                        $requisites[$requisite] = $extra_data->getCompanyKbe();
                        break;
                    case 'knp':
                        $requisites[$requisite] = $extra_data->getCompanyKnp();
                        break;
                }
            }
        }
        return $requisites;
    }

    /**
     * Данные по адресу организации.
     * @return array
     */
    private function getLocationPart() {
        $firm = $this->getFirm();
        /** @var Location $city */
        $city = $firm->getCity()->one();
        $city_string = ($city) ? $city->title : '';
        return [
            'city_id' => ($city) ? $city->id : 0,
            'city' => $city_string,
            'cities' => (new Location())->getCitiesArray(),
            'address' => RegionHelper::i()
                            ->setRegionId($firm->region_id)
                            ->setAddress($firm->legal_address)
                            ->setUseCity(false)
                            ->get(),
        ];
    }

    /**
     * @return Request
     * @throws Exception
     */
    public function getRequest() {
        if (is_null($this->request)) {
            throw new Exception('Commercial request model not set');
        }
        return $this->request;
    }

    /**
     * @return Product
     * @throws Exception
     */
    public function getProduct() {
        if (is_null($this->product)) {
            throw new Exception('Product model not set');
        }
        return $this->product;
    }

    /**
     * @return Firm
     */
    private function getFirm() {
        if ($this->use_response_firm_owner) {
            return $this->response->getFirmOwner();
        }
        return Firm::get();
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product) {
        $this->product = $product;
        return $this;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request) {
        $this->request = $request;
        return $this;
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response) {
        $this->response = $response;
        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Использовать ли данные с организации владельца коммерческого предложения.
     * @param bool $flag
     * @return $this
     */
    public function setUseResponseOwner($flag = true) {
        $this->use_response_firm_owner = $flag;
        return $this;
    }
}