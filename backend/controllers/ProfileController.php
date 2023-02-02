<?php

namespace backend\controllers;

use backend\forms\profile\Indexer;
use backend\forms\profile\Search;
use backend\forms\profile\Update;
use common\models\firms\Profile;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\Response;

/**
 * Class ProfileController
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class ProfileController extends BaseController
{

    public function actionIndex()
    {
        $model = new Profile();
        $data_provider = $model->search(\Yii::$app->request->get());
        $this->hideLeftMenu();
        $search_form = (new Search())
            ->setId('profile-search')
            ->addDataAttribute('url', Url::to(['profile/index']))
            ->setTitle('Поиск по организациям')
            ->setGetMethod()
            ->setFormMode(Search::FORM_HORIZONTAL_MODE)
            ->setTemplateFileName('index') //backend/views/profile/form/index
        ;
        if (\Yii::$app->request->get()) {
            $search_form->setFormData(\Yii::$app->request->get());
        }
        $indexer_form = (new Indexer())
            ->setId('profile-indexer-form')
            ->setAction(\Yii::$app->urlManager->createUrl('profile/calculate-part-count'))
            ->setTemplateFileName('indexer');
        return $this->render('index', [
            'data_provider' => $data_provider,
            'search_form' => $search_form->render(),
            'indexer_form' => $indexer_form->render(),
        ]);
    }

    /**
     * Обновление профиля организации.
     * @return string
     * @throws Exception
     */
    public function actionUpdate()
    {
        $id = (int)\Yii::$app->request->get('id', 0);
        /**
         * @var $profile Profile
         */
        if ($id) {
            $profile = Profile::findOne($id);
            if (!$profile) {
                throw new Exception('Профиля организации не существует');
            }
        } else {
            $profile = new Profile();
        }

        $profile_form = (new Update())
            ->setModel($profile)
            ->setPostMethod();

        if ($data = \Yii::$app->request->post()) {
            $profile_form->setFormData($data);
            if ($profile_form->save()) {
                if ($profile->isNewRecord) {
                    $this->refresh();
                } else {
                    $this->redirect(['profile/update', 'id' => $profile->id]);
                }
            }
        }

        return $this->render('update', [
            'profile' => $profile,
            'profile_form' => $profile_form->render(),
        ]);
    }

    /**
     * Индексация порции профилей организаций.
     * @return array
     * @throws Exception
     */
    public function actionIndexer()
    {
        if (\Yii::$app->request->isAjax) {
            $offset = \Yii::$app->request->post('offset', 0);
            $limit = \Yii::$app->request->post('limit', 0);
            // индексация партии профилей организаций
            Profile::indexPart($offset, $limit);
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => 1];
        } else {
            throw new Exception(404);
        }
    }

    /**
     * Метод очищает индекс. (truncate).
     * @return array
     * @throws Exception
     */
    public function actionClearIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $this->layout = false;
            \Yii::$app->response->format = Response::FORMAT_JSON;
            Profile::clearIndex();
            return ['success' => 1];
        } else {
            throw new Exception(404);
        }
    }

    /**
     * Таблица с порциями для индексации.
     * @return string
     * @throws Exception
     */
    public function actionCalculatePartCount()
    {
        if (\Yii::$app->request->isAjax) {
            $this->layout = false;
            $part_count = \Yii::$app->request->post('part_count', 10000);
            $data = Profile::calculateParts($part_count);
            return $this->renderFile(\Yii::getAlias('@app/views/common/parts-table.php'), [
                'parts' => $data,
                'limit' => $part_count,
            ]);
        } else {
            throw new Exception(404);
        }
    }
}