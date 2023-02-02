<?

namespace frontend\components\widgets;

use common\models\goods\Product;
use common\models\goods\search\Product as ProductFilter;
use yii\base\Widget;

/**
 * Class SimilarProducts
 * @package frontend\components\widgets
 * @author Артём Широких kowapssupport@gmail.com
 */
class SimilarProducts extends Widget {

    const SIMILAR_LIMIT = 4;
    //const COUNT_IN_ROW = 2;

    /** @var integer */
    public $product_id;

    public function run() {

        if (!$this->product_id) {
            return '';
        }

        $product = Product::getByIdCached($this->product_id);
        if (!$product) {
            return '';
        }

        $similar_products = $this->getSimilarProducts($product);
        //$similar_products = array_chunk($similar_products, self::COUNT_IN_ROW);

        return $this->render('similar-products', [
            'product_list' => $similar_products,
        ]);
    }

    /**
     * @param Product $product
     * @return array|Product[]
     */
    private function getSimilarProducts(Product $product) {
        $categories = $product->getCategoryIds();
        $product_filter = (new ProductFilter())
            ->setFilterCategories($categories)
            ->setFilterNotInId([$product->id])
            ->setFilterStatus(Product::STATUS_PRODUCT_ACTIVE);
        $products = $product_filter
            ->get()
            ->limit(self::SIMILAR_LIMIT)
            ->all()
        ;
        return $products;
    }
}