<?php

namespace frontend\controllers;

use common\libs\Env;
use common\libs\mail\SendMail;
use common\models\{Category, MainSlider, SoonEmail, User};
use common\models\firms\Profile;
use common\models\goods\search\Product as ProductSearch;
use frontend\components\lib\CompareProcessor;
use frontend\components\widgets\{b2b\Compare as CompareB2B, b2b\Favorite as FavoriteB2B, Compare, Favorite};
use frontend\forms\Search;
use frontend\forms\site\{b2b\Register, b2b\Sign, Join, ResetPassword, SoonForm};
use frontend\models\form\Join as JoinModel;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Site controller
 * @author Артём Широких kowapssupport@gmail.com
 */
class SiteController extends BaseController
{
    public function behaviors(): array
    {
        if (Env::i()->isProd()) {
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
                                'index', 'get-product-list', 'register', 'sign', 'update-compare-widget', 'login-backend',
                                'reset-password', 'change-password', 'error', // кроме
                            ],
                            'roles' => [],
                        ],
                    ],
                ]
            ];
        }
        return [];
    }

    //Главная страница сайта.
    public function actionIndex(): string
    {
        return $this->landingAction();
    }

    //Метод выполняет действия для главной страницы авторизованного пользователя.
    private function loggedAction(): string
    {
        $this->registerScriptBundle('site_logged');
        $search_form = $this->getSearchForm();
        return $this->render('logged', [
            'search_form' => $search_form->render(),
        ]);
    }

    //Метод выполняет действия для страницы лендинга (не авторизованный пользователь).
    private function landingAction(): string
    {
        $this->layout = 'b2b';
        $this->seo->title = 'GoodTrade.kz - Ваше пространство для бизнеса';

        $this->registerScriptBundleB2B();
        $search_form = $this->getSearchForm();

        /** @var MainSlider[] $slides */
        $slides = MainSlider::find()
            ->where(['slide_id' => 0])
            ->orderBy(['id' => 'asc'])
            ->all();

        $array_by_slide = [];
        foreach ($slides as $slide) {
            $parts = MainSlider::find()
                ->where(['slide_id' => $slide->id])
                //чтоб элементы слайдера с типом 1x2 и 2x1 были первыми
                ->orderBy('length(type) desc')
                ->all();
            $array_by_slide[$slide->id] = $parts;
        }

        /**
         * @var Category[] $activities
         */
        $activities = Category::find()
            ->where(['parent' => 0])
            ->orderBy(['title' => 'asc'])
            ->all();

        $activity_array = [];

        $product_filter = new ProductSearch();
        foreach ($activities as $activity) {
            $counter_array = $product_filter->getProductFirmCountBy(array_merge($activity->getAllChildIds(), [$activity->id]));
            $counter_array['activity'] = $activity;
            $activity_array[] = $counter_array;
        }

        return $this->render('index', [
            'slides' => $slides,
            'array_by_slide' => $array_by_slide,
            'activity_array' => $activity_array,
            'search_form' => $search_form->render(),
        ]);
    }

    /**
     * Метод выполняет действия для временной заглушки.
     * @return string
     * @deprecated
     */
    private function capAction(): string
    {
        // временная заглушка.
        $this->layout = 'coming-soon';
        $form = (new SoonForm())
            ->addClass('form line')
            ->setTemplateFileName('soon')
            ->setModel(new SoonEmail())
            ->setPostMethod();

        if ($data = Yii::$app->request->post()) {
            $form->setFormData($data);
            $form->validate();
            if ($form->save()) {
                $this->refresh();
            }
        }

        return $this->render('soon', [
            'form' => $form->render(),
        ]);
    }

    //Страница регистрации.
    public function actionJoin(): array|string
    {
        // если у пользователя уже есть организация - отправляю в кабинет.
        //todo: какой то глюк с 302
//        if ($this->hasUserFirm() && Env::i()->isProd()) {
//            $this->redirect(Yii::$app->urlManager->createUrl(['cabinet']));
//        }

        $this->layout = 'register';
        $this->registerScriptBundle();
        $this->seo->title = 'Заполните данные о вашей организации';

        $join = (new Join())
            ->setAjaxMode()
            ->setPostMethod()
            ->setTemplateFileName('join')
            ->setId('register')
            ->setModel(new JoinModel());

        if ($join->hasTemporaryBin()) {
            /** @var Profile $firm_profile */
            $firm_profile = Profile::find()->where(['bin' => $join->getTemporaryBin()])->one();
            if ($firm_profile) {
                // пред заполнение формы из профиля организации найденной по БИН.
                $join->setFormData([
                    'company_title' => $firm_profile->title,
                ]);
            }
        }

        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            return $join->ajaxValidateAndSave();
        }

        return $this->render('join', [
            'form' => $join->render(),
        ]);
    }

    //Форма поиска на главной странице сайта.
    public function getSearchForm(): Search
    {
        $search_form = parent::getSearchForm();
        $search_form->setTemplatePath(Yii::getAlias('@frontend/views/site'));
        $search_form->getProductQueryControl()->setPlaceholder('Поиск по товарам и компаниям');
        return $search_form;
    }

    //Вход.
    public function actionSign(): array|Response
    {
        $form = (new Sign())
            ->setModel(new User());
        $result = $form->ajaxValidateAndSave(['back_url' => Yii::$app->request->referrer]);
        return (empty($result)) ? $this->redirect(Yii::$app->request->referrer) : $result;
    }

    //Восстановление пароля.
    public function actionResetPassword(): array|Response
    {
        $form = (new ResetPassword())
            ->setModel(new User());
        $result = $form->ajaxValidateAndSave();
        return (empty($result)) ? $this->redirect(['site/index']) : $result;
    }

    //Смена пароля после восстановления.
    public function actionChangePassword(): string
    {
        $reset_token = Yii::$app->request->get('token');
        if (!$reset_token) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->pageNotFound();
        }
        /** @var User $user */
        $user = User::find()->where(['password_reset_token' => $reset_token])->one();
        if (is_null($user)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->pageNotFound();
        }
        $random_password = User::generateRandomPassword();
        $user->setPassword($random_password);
        // генерирую новый пароль, сбрасываю хеш восстановления и отправляю пароль на почту.
        $user->removePasswordResetToken();
        if ($result = $user->save()) {
            $result = SendMail::i()->setTo($user->email)
                ->setUser($user)
                ->sendNewPassword($random_password);
        }
        $this->registerCommonBundle();
        return $this->render('change-password', [
            'user' => $user,
            'result' => $result,
        ]);
    }

    /**
     * Отдаёт обновлённые данные виджета сравнения.
     * @throws \Exception
     */
    public function actionUpdateCompareWidget(): string
    {
        $this->layout = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ((int)Yii::$app->request->post('b2b', 0)) {
            return CompareB2B::widget();
        }
        return Compare::widget();
    }

    /**
     * Отдаёт обновлённые данные виджета избранных.
     * @throws \Exception
     */
    public function actionUpdateFavoriteWidget(): string
    {
        $this->layout = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ((int)Yii::$app->request->post('b2b', 0)) {
            return FavoriteB2B::widget();
        }
        return Favorite::widget();
    }

    //Выход.
    public function actionLogout(): Response
    {
        if (isset($_COOKIE[CompareProcessor::getCookieKey()])) {
            unset($_COOKIE[CompareProcessor::getCookieKey()]);
        }
        Yii::$app->user->logout();
        return $this->redirect(Yii::$app->request->referrer);
    }

    //Обработка регистрации (форма в футере сайта).
    public function actionRegister(): array|Response
    {
        $form = (new Register())
            ->setModel(new User());
        $result = $form->ajaxValidateAndSave();
        return (empty($result)) ? $this->redirect(['site/index']) : $result;
    }

    /**
     * Авторизация пользователя с административной панели.
     * @return Response
     * @throws \Exception
     */
    public function actionLoginBackend(): Response
    {
        $this->layout = false;
        if (mb_strpos(Yii::$app->request->referrer, Env::i()->getBackendUrl()) === false) {
            throw new Exception('Page not found', 404);
        }
        $user_id = (int)Yii::$app->request->get('user_id', 0);
        $token = (string)Yii::$app->request->get('token');
        /** @var User $user */
        $user = User::find()->where(['id' => $user_id, 'auth_token' => $token])->one();
        if (null === $user) {
            throw new Exception('Page not found', 404);
        }
        $user->removeAuthToken();
        //Yii::$app->user->logout();
        $login_result = Yii::$app->user->login($user);
        if ($login_result) {
            return $this->redirect(['/']);
        } else {
            throw new \Exception('Page not found', 404);
        }
    }
}
