<?php

/**
 * @var \main\models\DataConfig $dataConfig
 * @var \main\models\MarkupComponent $component
 */

$componentParentsFromTop = function ($component) {
    $current = $component;
    $stack = [];
    while ($current = $current->getParentComponent()) {
        array_unshift($stack, $current);
    }
    foreach ($stack as $item) {
        yield $item;
    }
}

?>
<style>
    .component-about .component-name::first-letter {
        text-transform: uppercase;
    }
    .component-about .more-info {
        display: block;
        margin-bottom: 1em;
        border-bottom: 1px solid #CCC;
    }
</style>

<div class="container component-about">
    <h1><?= $component->sid ?>
        <small>
            <?php $current = $component ?>
            <?php while ($current = $current->getParentComponent()) : ?>
                « <a href="<?= \yii\helpers\Url::to(['site/component-about', 'sid' => $current->sid]) ?>"><?= $current->sid?></a>
            <?php endwhile; ?>
        </small>
    </h1>

    <h2 class="component-name"><?= $component->name ?></h2>

    <?php foreach ($componentParentsFromTop($component) as $parent) : ?>
        <?= $parent->getTldr() ?>
        <?= $parent->getHasDocument('README') ? \yii\helpers\Html::a('Подробнее »', ['/site/component-about', 'sid' => $parent->sid], ['class' => 'more-info']) : '' ?>
        <?= $parent->explain($component) ?>
    <?php endforeach; ?>


    <?= $component->getTldr() ?>
    <?= $component->explain($component) ?>
    <?= $component->getReadme() ?>
</div>

