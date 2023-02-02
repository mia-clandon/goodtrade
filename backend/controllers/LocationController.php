<?php /** @noinspection PhpUndefinedFieldInspection */

namespace backend\controllers;

use backend\forms\location\{Search, Update};
use common\models\Location;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\Response;

/**
 * Class LocationController
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class LocationController extends BaseController
{
    public function actionIndex(): string
    {
        $model = new Location();
        $data_provider = $model->search(\Yii::$app->request->get());
        $search_form = (new Search())
            ->addDataAttribute('url', Url::to(['location/index']))
            ->setTitle('Поиск по локациям')
            ->setGetMethod()
            ->setFormMode(Search::FORM_HORIZONTAL_MODE)
            ->setTemplateFileName('index'); //backend/views/location/form/index

        if (\Yii::$app->request->get()) {
            $search_form->setFormData(\Yii::$app->request->get());
        }
        return $this->render('index', [
            'data_provider' => $data_provider,
            'search_form' => $search_form->render(),
        ]);
    }

    public function actionUpdate(): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $model = $this->getModel();
        $location_form = (new Update())
            ->setModel($model)
            ->setPostMethod();

        if ($data = \Yii::$app->request->post()) {
            $location_form->setFormData($data);
            $location_form->validate();

            if ($location_form->save()) {
                if ($location_form->isNewRecord) {
                    $this->refresh();
                } else {
                    $this->redirect(['location/update', 'id' => $model->id]);
                }
            }
        }
        return $this->render('update', [
            'location' => $model,
            'location_form' => $location_form->render(),
        ]);
    }

    /**
     * @return Response
     * @throws Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(): Response
    {
        $id = (int)\Yii::$app->request->get('id', 0);
        $model = Location::findOne($id);
        if (!$model) {
            throw new Exception('Location not found.');
        }
        $model->delete();
        return $this->redirect(['location/index']);
    }

    public function actionGetLocationsByRegion(): array
    {
        $region_id = \Yii::$app->request->post('region_id');
        $this->layout = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if ($region_id) {
            return Location::find()
                ->where(['region' => $region_id])
                ->select(['id', 'title'])
                ->asArray()
                ->all();
        }
        return [];
    }

    //Переиндексация.
    public function actionUpdateIndex(): Response
    {
        Location::indexAll();
        \Yii::$app->getSession()->setFlash(BaseController::FLASH_MESSAGE_SUCCESS, 'Индекс обновился успешно.');
        return $this->redirect(['location/index']);
    }

    /**
     * @return Location
     * @throws Exception
     */
    public function getModel(): Location
    {
        $id = (int)\Yii::$app->request->get('id', 0);
        /**
         * @var $model Location
         */
        if ($id) {
            $model = Location::findOne($id);
            if (!$model) {
                throw new Exception('Города не существует');
            }
        } else {
            $model = new Location();
        }
        return $model;
    }
}
