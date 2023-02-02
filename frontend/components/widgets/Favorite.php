<?

namespace frontend\components\widgets;

use yii\base\Widget;
use yii\db\Query;

use common\models\firms\Firm;
use common\models\goods\Product;

use frontend\components\lib\FavoriteProcessor;

/**
 * Class Compare
 * Виджет отображающий компании и товары избранного.
 * @package frontend\components\widgets
 * @author yergant
 */
class Favorite extends Widget {

    const FIRM_TITLE_PROPERTY = 'firm_title';
    const PRODUCT_COUNT_PROPERTY = 'product_count';
    const PRODUCT_IDS_PROPERTY = 'product_ids';
    const FIRM_ID_PROPERTY = 'firm_id';

    /**
     * Получение списка избранных товаров.
     * @return Product[]
     */
    protected function getData() {
        $firm_product_ids = FavoriteProcessor::i()->getFavoriteIds();
        $firm_data = !empty(array_keys($firm_product_ids)) ? (new Query())
            ->select([
                'f.title AS '.self::FIRM_TITLE_PROPERTY,
                'f.id AS '.self::FIRM_ID_PROPERTY
            ])
            ->from(Firm::tableName().' f')
            ->where(['f.id' => array_keys($firm_product_ids)])
            ->all() : [];

        $favorite_data = [];

        foreach ($firm_data as $firm) {
            $data = [];
            $data[self::FIRM_TITLE_PROPERTY] = $firm[self::FIRM_TITLE_PROPERTY];
            $data[self::FIRM_ID_PROPERTY] = $firm[self::FIRM_ID_PROPERTY];
            $data[self::PRODUCT_COUNT_PROPERTY] = count($firm_product_ids[$firm[self::FIRM_ID_PROPERTY]]);
            $data[self::PRODUCT_IDS_PROPERTY] = $firm_product_ids[$firm[self::FIRM_ID_PROPERTY]];

            $favorite_data[] = $data;
        }

        if(array_key_exists(0, $firm_product_ids)) {
            $data = [];
            $data[self::FIRM_TITLE_PROPERTY] = 'Нет компании';
            $data[self::FIRM_ID_PROPERTY] = 0;
            $data[self::PRODUCT_COUNT_PROPERTY] = count($firm_product_ids[0]);
            $data[self::PRODUCT_IDS_PROPERTY] = $firm_product_ids[0];

            $favorite_data[] = $data;
        }

        return $favorite_data;
    }

    public function run() {
        $favorite_data = $this->getData();
        return $this->render('favorite', [
            'favorite_data' => $favorite_data,
            'favorite_count' => count($favorite_data),
        ]);
    }
}