<?
/**
 * Характеристики товара подгружаемые в модальное окно.
 * @var \common\models\goods\Product $product
 * @author Артём Широких kowapssupport@gmail.com
 */

use common\models\goods\helpers\Vocabulary;
use frontend\components\form\controls\Choose;

/*
 * todo: так как пока пользователь может выбирать и так только 1 значение то - вывод характеристик не нужен.
$vocabulary_decorated_data = $product->getVocabularyHelper()->getDecoratedValues();
foreach ($vocabulary_decorated_data as $vocabulary_data) {

    $vocabulary_name = $vocabulary_data[Vocabulary::KEY_VOCABULARY][Vocabulary::KEY_VALUE];

    $value_id = $vocabulary_data[Vocabulary::KEY_VOCABULARY][Vocabulary::KEY_ID];
    $value_name = $vocabulary_data[Vocabulary::KEY_VOCABULARY_VALUE][Vocabulary::KEY_VALUE];

    // todo: Отображаю в списке только те характеристики у которых больше 1 значения.
    echo (new Choose())
        ->setTitle($vocabulary_name)
        ->setArrayOfOptions([$value_id => $value_name])
        ->setName('terms')
        ->setIsMultiple()
        ->render();
}

echo (new \frontend\components\form\controls\Checkbox())
    ->setTitle('На все модификации')
    ->setName('is_all_modifications')
    ->render();

*/