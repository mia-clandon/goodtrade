<?
/**
 * Часть шаблона отвечает за вывод списка категорий 1го уровня.
 * @var array $category_list
 * @author Артём Широких kowapssupport@gmail.com
 */

use common\models\CategoryVocabulary;

if (count($category_list) > 0) { ?>
<ul class="categories-list">
    <? foreach ($category_list as $category) {
        $vocabularies = CategoryVocabulary::getVocabulariesByCategory($category['id']);
    ?>
        <li data-category-id="<?= $category['id']; ?>" class="categories-list__category-item <?= $category['has_child'] ? 'categories-list__category-item_parent' : '';?>">
            <a data-category-id="<?= $category['id']; ?>" class="categories-list__category-item-link ajax" href="<?= Yii::$app->urlManager->createUrl(['/category/update', 'id' => $category['id']]);?>">
                <?= $category['title']; ?>
            </a>
            <div class="categories-list__category-item-actions">
                (<a target="_blank" href="<?= Yii::$app->urlManager->createUrl(['/category/update', 'parent_id' => $category['id']]);?>" class="categories-list__category-item-action-link">[Добавить подкатегорию]</a>
                <a data-category-id="<?= $category['id']; ?>" href="#" class="categories-list__category-item-action-link oked-relation-link">[Привязать ОКЭД]</a>
                <a href="<?= Yii::$app->urlManager->createUrl(['/category/update', 'id' => $category['id']]);?>" class="categories-list__category-item-action-link">[Редактировать]</a>
                <a
                    href="<?= Yii::$app->urlManager->createUrl(['/category/delete', 'id' => $category['id']]);?>"
                    class="categories-list__category-item-action-link remove text-danger"
                    data-confirm="Вы действительно хотите удалить категорию ?"
                >
                    [Удалить]
                </a>)
            </div>
            <div class="categories-list__specifications-list">
                <? foreach ($vocabularies as $vocabulary) { ?>
                    <span>
                        <a href="#" class="categories-list__specification-item"><?= $vocabulary->title; ?></a>
                        <a href="#" data-vocabulary-id="<?= $vocabulary->id; ?>" data-category-id="<?= $category['id']; ?>"
                           class="remove-vocabulary" title="Отвязать характеристику">
                            <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
                        </a>,
                    </span>
                <? } ?>
                <a href="#" data-category-id="<?= $category['id']; ?>" class="add-category-vocabulary">[Привязать характеристику]</a>
            </div>
        </li>
    <? } ?>
</ul>
<? } ?>