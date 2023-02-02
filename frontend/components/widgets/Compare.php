<?

namespace frontend\components\widgets;

use yii\base\Widget;
use yii\db\Query;

use common\models\Category;
use common\models\goods\Categories;
use common\models\goods\Product;

use frontend\components\lib\CompareProcessor;

/**
 * Class Compare
 * Виджет отображающий категории товаров и товары для сравнения в шапке сайта.
 * @package frontend\components\widgets
 * @author Артём Широких kowapssupport@gmail.com
 */
class Compare extends Widget {

    const CATEGORY_TITLE_PROPERTY = 'category_title';
    const PRODUCT_COUNT_PROPERTY = 'product_count';
    const PRODUCT_IDS_PROPERTY = 'product_ids';
    const CATEGORY_ID_PROPERTY = 'category_id';

    /**
     * Получение списка избранных товаров.
     * @return Product[]
     */
    protected function getData() {
        $product_ids = CompareProcessor::i()->getCompareProductIds();
        return !empty($product_ids) ? (new Query())
            ->select([
                'c.title AS '.self::CATEGORY_TITLE_PROPERTY,
                'count(pc.product_id) AS '.self::PRODUCT_COUNT_PROPERTY,
                'group_concat(pc.product_id) AS '.self::PRODUCT_IDS_PROPERTY,
                'c.id AS '.self::CATEGORY_ID_PROPERTY
            ])
            ->from(Categories::tableName().' pc')
            ->innerJoin(['c' => Category::tableName()], 'c.id = pc.category_id')
            ->where(['pc.product_id' => $product_ids])
            ->groupBy(['c.id'])
            ->all() : [];
    }

    public function run() {
        $compare_data = $this->getData();
        return $this->render('compare', [
            'compare_data' => $compare_data,
            'compare_count' => count($compare_data),
        ]);
    }
}