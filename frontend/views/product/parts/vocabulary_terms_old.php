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
    <tr class="tech-specs-row">
        <td class="tech-specs-key">
            <div><span><?= $vocabulary_name; ?></span></div>
        </td>
        <td class="tech-specs-value"><?= $value; ?></td>
    </tr>
<? } ?>