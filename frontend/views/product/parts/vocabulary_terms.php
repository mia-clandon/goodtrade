<?
/**
 * Вывод характеристик товара. (на странице просмотра товара)
 * @var \common\models\goods\Product $product
 */

use common\models\goods\helpers\Vocabulary;

$vocabulary_decorated_data = $product->getVocabularyHelper()->getDecoratedValues();
foreach ($vocabulary_decorated_data as $vocabulary_data) {
    $vocabulary_name = $vocabulary_data[Vocabulary::KEY_VOCABULARY][Vocabulary::KEY_VALUE];
    $value = $vocabulary_data[Vocabulary::KEY_VOCABULARY_VALUE][Vocabulary::KEY_VALUE];
?>
    <div class="tech-specs__row row">
        <div class="col-lg col-lg_two-thirds">
            <div class="tech-specs__name">
                <span><?= $vocabulary_name; ?></span>
            </div>
            <div class="tech-specs__dots"></div>
        </div>
        <div class="col-lg col-lg_third">
            <div class="tech-specs__value"><?= $value; ?></div>
        </div>
    </div>
<? } ?>