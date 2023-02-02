<?

/**
 * Вывод характеристик товара. (на странице коммерческого предложения)
 * @var \common\models\goods\Product $product
 */

use common\models\goods\helpers\Vocabulary;

$vocabulary_decorated_data = $product->getVocabularyHelper()->getDecoratedValues();
    foreach ($vocabulary_decorated_data as $vocabulary_data) {
        $vocabulary_name = $vocabulary_data[Vocabulary::KEY_VOCABULARY][Vocabulary::KEY_VALUE];
        $value = $vocabulary_data[Vocabulary::KEY_VOCABULARY_VALUE][Vocabulary::KEY_VALUE];
?>

<div class="page-product-description-row clearfix">
    <div class="page-col-left is-relative">
        <div class="page-product-description-space"></div>
        <div class="page-product-description-name"><?= $vocabulary_name; ?></div>
    </div>
    <div class="page-col-right">
        <div class="page-product-description-value"><?= $value; ?></div>
    </div>
</div>
<? } ?>