<?php

namespace frontend\controllers;

use common\models\commercial\Request;
use common\models\commercial\Response;
use common\models\firms\Firm;
use common\models\goods\Product;
use common\models\Location;
use frontend\assets\CommercialResponse;
use frontend\components\lib\CommercialResponseProcessor;
use frontend\components\widgets\CommercialRequest;
use frontend\forms\commercial\Request as CommercialRequestForm;
use frontend\forms\commercial\RequestAll as CommercialRequestFormAll;
use frontend\forms\commercial\RequestNew as CommercialRequestFormNew;
use frontend\models\commercial\ResponseData;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class CommercialController
 * @package frontend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class CommercialController extends BaseController
{

    const TEMPLATE_NAME_COMMERCIAL_REQUEST_FORM = 'commercial-request';
    const TEMPLATE_NAME_COMMERCIAL_REQUEST_NEW_FORM = 'commercial-request-new';

    private $commercial_request_modal_version = CommercialRequest::MODAL_OLD;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // разрешаю все авторизованным.
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'get-request-form',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Отображение коммерческого предложения.
     * @return string
     * @throws Exception
     */
    public function actionShowResponse()
    {
        $this->layout = 'commercial-response';

        $response = (int)\Yii::$app->request->get('response');
        if (!$response) {
            throw new Exception('Страница не найдена');
        }
        /** @var Response $response */
        $response = Response::find()
            ->where(['id' => $response,])
            ->andWhere(['not in', 'status', [Response::STATUS_DELETED]])
            ->one();
        if (!$response) {
            throw new Exception('Страница не найдена');
        }
        // владелец или получатель должна быть "моя организация".
        if (!in_array(Firm::get()->id, [$response->to_firm_id, $response->from_firm_id])) {
            throw new Exception('Страница не найдена');
        }
        /** @var Product $product */
        $product = $response->getProduct()->one();
        /** @var Request $request */
        $request = $response->getRequest()->one();

        $response_processor = CommercialResponseProcessor::i()
            ->setProduct($product)
            ->setRequest($request)
            ->setResponse($response)
            ->setUseResponseOwner();

        $this->registerCommonBundle();
        CommercialResponse::register($this->getView());

        return $this->render('response/show', [
            'response' => $response,
            'request' => $request,
            'product' => $product,
            'response_owner_firm' => $response->getFirmOwner(),
            'commercial_data' => $response_processor->getCommerceSettings(),
        ]);
    }

    /**
     * Коммерческий ответ пользователю на запрос.
     * @return string
     * @throws Exception
     */
    public function actionResponse()
    {
        $this->layout = 'commercial-response';
        $request_id = (int)\Yii::$app->request->get('request');
        /** @var Request $request */
        $request = Request::findOne($request_id);
        if (!$request) {
            throw new Exception('Коммерческий запрос не найден.');
        }
        // для меня ли был запрос?.
        if ($request->to_firm_id != Firm::get()->id) {
            throw new Exception('Страница не найдена.');
        }
        /** @var Product $product */
        $product = Product::findOne($request->product_id);
        if (!$product || $product->status != Product::STATUS_PRODUCT_ACTIVE) {
            throw new Exception('Страница не найдена.');
        }

        if ($request->status == Request::STATUS_ANSWERED) {
            // если пользователь уже ответил на коммерческий запрос.
            //todo: flash message
            return $this->redirect(['/']);
        }

        if (\Yii::$app->request->isPost) {
            $this->layout = false;
            $response_data = \Yii::$app->request->post();

            $response_result = (new Response())
                ->setRequestId($request_id)
                ->setFromFirmId(Firm::get()->id)
                ->setProductCount(ArrayHelper::getValue($response_data, Response::PROP_PRODUCT_COUNT))
                ->setInStock((bool)ArrayHelper::getValue($response_data, Response::PROP_PRODUCT_IN_STOCK))
                ->setDaysToSend(ArrayHelper::getValue($response_data, Response::PROP_TIME_TO_SEND))
                ->setPrePayment(ArrayHelper::getValue($response_data, Response::PROP_PRE_PAYMENT))
                ->setPostPayment(ArrayHelper::getValue($response_data, Response::PROP_POST_PAYMENT))
                ->setResponseValidity(ArrayHelper::getValue($response_data, Response::PROP_RESPONSE_VALIDITY))
                ->setProductPrice(ArrayHelper::getValue($response_data, Response::PROP_PRODUCT_PRICE))
                ->setWithVat((bool)ArrayHelper::getValue($response_data, Response::PROP_PRODUCT_WITH_VAT))

                // данные по организации (extra_data).
                ->setResponseObject((new ResponseData())
                    ->setDeliveryTerms(ArrayHelper::getValue($response_data, Response::PROP_DELIVERY_CONDITION, []))
                    ->setCompanyCity(ArrayHelper::getValue($response_data, Response::PROP_COMPANY_CITY))
                    ->setCompanyAddress(ArrayHelper::getValue($response_data, Response::PROP_COMPANY_ADDRESS))
                    ->setCompanyEmails(ArrayHelper::getValue($response_data, Response::PROP_COMPANY_EMAIL, []))
                    ->setCompanyPhones(ArrayHelper::getValue($response_data, Response::PROP_COMPANY_PHONE, []))
                    ->setCompanyBin(ArrayHelper::getValue($response_data, Response::PROP_COMPANY_BIN))
                    ->setCompanyBank(ArrayHelper::getValue($response_data, Response::PROP_COMPANY_BANK))
                    ->setCompanyBik(ArrayHelper::getValue($response_data, Response::PROP_COMPANY_BIK))
                    ->setCompanyIik(ArrayHelper::getValue($response_data, Response::PROP_COMPANY_IIK))
                    ->setCompanyKbe(ArrayHelper::getValue($response_data, Response::PROP_COMPANY_KBE))
                    ->setCompanyKnp(ArrayHelper::getValue($response_data, Response::PROP_COMPANY_KNP))
                )
                // отправляет коммерческое предложение пользователю.
                ->sendResponse();
            if ($response_result) {
                // todo: добавить notification.
                return $this->redirect(\Yii::$app->urlManager->createUrl(['/']));
            }
        }

        $this->registerScriptBundle();
        CommercialResponse::register($this->getView());
        $request_data = $request->getRequestData();
        /** @var Firm $request_owner */
        $request_owner = $request->getFirmOwner();

        $response_processor = CommercialResponseProcessor::i()
            ->setProduct($product)
            ->setRequest($request);
        // общий список дней действия коммерческого предложения.
        $response_validity = (new Response())->getResponseValidity();

        return $this->render('response/response', [
            'request' => $request,
            'request_data' => $request_data,
            'product' => $product,
            'request_owner' => $request_owner,
            'firm' => Firm::get(),
            'commercial_data' => $response_processor->getCommerceSettings(),
            'cities' => (new Location())->getCitiesArray(),
            'response_validity' => $response_validity,
            'delivery_terms' => $product->getDeliveryTermsHelper()->getAllDeliveryTerms(),
        ]);
    }

    /**
     * Отправка коммерческого запроса.
     * @return array
     * @throws Exception
     */
    public function actionSendRequest()
    {
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        $new_type = $_POST['new_type'] ?? false;
        $form = $new_type
            ? (new CommercialRequestForm())
            : (new CommercialRequestFormNew());

        $commercial_form = $form
            ->setModel((new Request()))
            ->setFormData(\Yii::$app->request->post());
        return $commercial_form->ajaxValidateAndSave([
            'request_validity' => \Yii::$app->request->post('request_validity', 0),
        ]);
    }

    /**
     * Отправка общего коммерческого запроса .
     * @return array
     * @throws Exception
     */
    public function actionSendRequestAll()
    {
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }
        $commercial_form = (new CommercialRequestFormAll())
            ->setModel((new Request()))
            ->setFormData(\Yii::$app->request->post());
        return $commercial_form->ajaxValidateAndSave(['request_validity' => \Yii::$app->request->post('request_validity', 0)]);
    }

    /**
     * Получение формы коммерческого запроса на товар.
     * @return string
     * @throws Exception
     */
    public function actionGetRequestForm()
    {
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        $this->layout = false;
        $product_id = \Yii::$app->request->post('product_id', 0);
        $modal_version = \Yii::$app->request->post('version', CommercialRequest::MODAL_OLD);
        $modal_version = $modal_version === 'new'
            ? CommercialRequest::MODAL_NEW
            : CommercialRequest::MODAL_OLD;

        if ((bool)\Yii::$app->user->isGuest) {
            // возврат вёрстки для неавторизованного пользователя.
            return $this->getRequestNotAuthorized($modal_version);
        } else {
            $product_ids = explode(',', $product_id);
            $product_ids = array_filter($product_ids, 'intval');

            if (count($product_ids) > 1) {
                // только для старой вёрстки т.к. таблица сравнения - старая вёрстка.
                return $this->getRequestAuthorizedAll($product_ids);
            } else {
                $product_id = (int)$product_id;
                $product = Product::getByIdCached($product_id);
                if (null === $product) {
                    throw new Exception('Товара не существует.');
                }
                return $this->getRequestAuthorized($product, $modal_version);
            }
        }
    }

    /**
     * Форма для авторизованного пользователя.
     * @param Product $product
     * @param int $version
     * @return string
     */
    private function getRequestAuthorized(Product $product, int $version = CommercialRequest::MODAL_OLD)
    {

        if ($version === CommercialRequest::MODAL_OLD) {
            // Форма для старой вёрстки...
            $request_form = (new CommercialRequestForm())
                ->setTemplateFileName(self::TEMPLATE_NAME_COMMERCIAL_REQUEST_FORM)
                ->setAjaxMode()
                ->setPostMethod()
                ->setAction(Url::to(['commercial/send-request']))
                ->setFirmId($product->firm_id)
                ->setProductId($product->id);
        } else {
            // Форма для новой вёрстки...
            $request_form = (new CommercialRequestFormNew())
                ->setTemplateFileName(self::TEMPLATE_NAME_COMMERCIAL_REQUEST_NEW_FORM)
                ->setAjaxMode()
                ->setUseNewCodingMode()
                ->setPostMethod()
                ->setAction(Url::to(['commercial/send-request']))
                ->setFirmId($product->firm_id)
                ->setProductId($product->id)
                ->setProduct($product);
        }

        /*
        $vocabulary_terms_html = \Yii::$app->getView()->renderFile(
            \Yii::getAlias('@frontend/views/product/parts/vocabulary_terms_for_modal.php'
        ),[
            'product' => $product,
        ]);
        */

        $view_file_name = $version === CommercialRequest::MODAL_NEW
            ? 'authorized-b2b'
            : 'authorized';

        // получение отрендеренных характеристик товара для модального окна.
        $request_form->addTemplateVars(['vocabulary_terms_html' => '']);

        return $this->render('request/' . $view_file_name, [
            'form' => $request_form->render(),
            'product' => $product,
        ]);
    }

    /**
     * Форма для авторизованного пользователя для всех.
     * @param array $product_ids
     * @return string
     */
    private function getRequestAuthorizedAll(array $product_ids)
    {

        $request_form = (new CommercialRequestFormAll())
            ->setTemplateFileName(self::TEMPLATE_NAME_COMMERCIAL_REQUEST_FORM)
            ->setAjaxMode()
            ->setPostMethod()
            ->setAction(Url::to(['commercial/send-request-all']))
            //todo wtf? ???
            ->setProductId(implode(',', $product_ids));

        $request_form->addTemplateVars(['vocabulary_terms_html' => '']);

        return $this->render('request/authorized-all', [
            'form' => $request_form->render(),
        ]);
    }

    /**
     * Форма для не авторизованного пользователя.
     * @param int $version
     * @return string
     */
    private function getRequestNotAuthorized(int $version = CommercialRequest::MODAL_OLD)
    {
        $view_file_name = $version === CommercialRequest::MODAL_NEW
            ? 'not-authorized-b2b'
            : 'not-authorized';
        return $this->render('request/' . $view_file_name);
    }

    /**
     * @param int $version
     * @return $this
     */
    public function setCommercialRequestModalVersion(int $version)
    {
        $this->commercial_request_modal_version = $version;
        return $this;
    }
}