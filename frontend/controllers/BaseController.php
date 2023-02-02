<?php

namespace frontend\controllers;

use common\libs\Env;
use common\models\firms\Firm;
use common\models\User;
use frontend\assets\{AppAsset, b2b\AppAsset as AppAssetB2B, BundleAsset, RequireJsAsset};
use frontend\components\lib\Breadcrumbs;
use frontend\forms\Search;
use Yii;
use yii\base\Response;
use yii\helpers\{ArrayHelper, Url};
use yii\web\{Controller, NotFoundHttpException, View};

/**
 * Class BaseController
 * @package frontend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class BaseController extends Controller
{

    const APP_SCRIPT_PATH = '/app/pages';
    const FILE_POSTFIX = '_bundle.js';

    /** @var null|Search */
    private $search_form = null;

    /** @var null|Breadcrumbs */
    private $breadcrumbs = null;

    /**
     * @var $seo \Amirax\SeoTools\models\SeoMeta
     */
    public $seo = null;

    public function actions()
    {
        return [
            'error' => ['class' => 'frontend\components\lib\ErrorAction'],
        ];
    }

    /**
     * Функция регистрирует на странице script у requireJs
     * @param string $script
     * @param boolean $load_config
     * @deprecated - отказался от require js.
     */
    public function registerRequireJsScript($script = null, $load_config = true)
    {

        $require_js = new RequireJsAsset();
        $require_js->depends = ['frontend/assets/AppAsset'];
        if (!is_null($script)) {
            $require_js->jsOptions = ['data-main' => $script];
        }
        if ($load_config) {
            $require_js->js = array_merge(
            // конфиг require.
                ['/js/pages/config.js'],
                // сам require.
                $require_js->js,
                // базовые скрипты страницы.
                ['/js/pages/base-scripts.js']
            );
        }
        $require_js->registerAssetFiles($this->getView());
    }

    /**
     * Регистрация скрипта action'a.
     * @param null|string $default_alias
     * @return $this;
     */
    public function registerScriptBundle($default_alias = null)
    {
        AppAsset::register(Yii::$app->getView());
        $this->registerScriptBundleFunc($default_alias);
        return $this;
    }

    /**
     * Регистрация скрипта action'a.(Новая верстка)
     * @param null|string $default_alias
     * @return $this;
     */
    public function registerScriptBundleB2B($default_alias = null)
    {
        AppAssetB2B::register(Yii::$app->getView());
        $this->registerScriptBundleFunc($default_alias);
        return $this;
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function pageNotFound(): void
    {
        throw new NotFoundHttpException();
    }

    private function registerScriptBundleFunc($default_alias = null)
    {
        $asset_bundle = new BundleAsset();
        $alias = is_null($default_alias) ? Yii::$app->controller->id . '_' . Yii::$app->controller->action->id : (string)$default_alias;
        $module_name = ArrayHelper::getValue(Yii::$app->controller->module, 'id', null);
        if (!is_null($module_name) && is_null($default_alias) && !in_array($module_name, ['app-frontend'])) {
            $alias = $module_name . '_' . $alias;
        }
        $alias = strtolower(str_replace('-', '_', $alias));
        $asset_bundle->js = [
            self::APP_SCRIPT_PATH . '/' . $alias . self::FILE_POSTFIX,
        ];
        $asset_bundle->registerAssetFiles(Yii::$app->getView());
        return $this;
    }

    /**
     * Регистрирует общие скрипты.
     * @return $this
     */
    public function registerCommonBundle()
    {
        $this->registerScriptBundle('common');
        return $this;
    }

    /**
     * Форма поиска (используется на главной странице и на странице товаров /product и тд.)
     * @return Search
     */
    public function getSearchForm(): Search
    {
        if (is_null($this->search_form)) {
            $this->search_form = (new Search())
                ->setTemplateFileName('search')
                ->setTemplatePath(\Yii::getAlias('@frontend/views/site'))
                ->setGetMethod()
                ->setAction(Url::to(['search/index']));
        }
        return $this->search_form;
    }

    /**
     * @param integer $product_count
     * @param integer $firms_count
     * @return $this
     * @deprecated удалить после страницы поиска.
     */
    public function setSearchFormInfo($product_count, $firms_count)
    {

        $search_form = $this->getSearchForm();

        // установка количества в поисковую форму.
        $search_form->setProductCount($product_count);
        $search_form->setFirmsCount($firms_count);

        // параметры запроса.
        $request_params = array_map('urldecode', \Yii::$app->request->get());

        $search_form->setActiveTumbler(Yii::$app->controller->id == 'product' ?
            Search::ACTIVE_TUMBLER_PRODUCTS : Search::ACTIVE_TUMBLER_FIRMS
        );

        // ссылки для переходов на разные виды отображения результатов.
        $search_form->setProductFindUrl(Url::to(array_merge(['product/index'], $request_params)));
        $search_form->setFirmFindUrl(Url::to(array_merge(['firm/index'], $request_params)));

        return $this;
    }

    protected function getUser(): ?User
    {
        return User::get();
    }

    public function getFirm(): Firm
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return Firm::get();
    }

    /**
     * Метод проверяет, есть ли у пользователя организация.
     * @return bool
     */
    protected function hasUserFirm(): bool
    {
        if ($user = $this->getUser()) {
            return $user->hasFirm();
        }
        return false;
    }

    /**
     * @param \yii\base\Action $action
     * @return bool|Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $this->seo = Yii::$app->get('seo');

        //TODO: временно.
        $this->enableCsrfValidation = false;

        $controllerString = Yii::$app->controller->id;
        $actionString = Yii::$app->controller->action->id;

        Yii::$app->view->registerJs('var controller = "' . $controllerString . '";', View::POS_BEGIN);
        Yii::$app->view->registerJs('var action = "' . $actionString . '";', View::POS_BEGIN);

        if (!Env::i()->isProd()) {
            \Yii::$app->view->registerCss('
                code {
                    position: absolute;
                    z-index: 9999;
                }
            ');
        }

        // если нет организации то перевожу на /join. (первый шаг).
        if ($action->id !== 'join' && !Yii::$app->user->isGuest && !Firm::hasFirm()) {
            return $this->redirect(['/join']);
        }

        return parent::beforeAction($action);
    }


    /**
     * @return Breadcrumbs
     */
    public function getBreadcrumbs()
    {
        if (is_null($this->breadcrumbs)) {
            $this->breadcrumbs = Breadcrumbs::i();
        }
        return $this->breadcrumbs;
    }

    /**
     * @return \frontend\components\lib\b2b\Breadcrumbs
     */
    public function getBreadcrumbsB2B()
    {
        if (is_null($this->breadcrumbs)) {
            $this->breadcrumbs = \frontend\components\lib\b2b\Breadcrumbs::i();
        }
        return $this->breadcrumbs;
    }
}