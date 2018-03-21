<?php

\main\assets\ComponentMarkerAsset::register($this);

/**
 * @var $widgets \main\models\WidgetTask[]
 * @var $preview \main\models\DesignPreview
 * @var $this \yii\web\View
 */
?>

<div class="container">
    <?php if (count($widgets) == 1) : ?>
    <?php $widgetTask = end($widgets) ?>
        <h1>Виджет <?= $widgetTask->sid ?> на странице <?= $preview->design->sid ?>.<?= $preview->sid ?></h1>
    <?php else : ?>
        <h1>Виджеты на странице <?= $preview->design->sid ?>.<?= $preview->sid ?></h1>
    <?php endif ?>

    <div class="row">
        <div class="col-sm-2">
            <div id="controls" style="position: fixed;">
                <?php foreach ($widgets as $widgetTask) : ?>
                    <?php foreach ($widgetTask->getPreviewCoords($preview) as $i => $coords) : ?>
                        <a
                            data-type="component" data-sid="<?= $widgetTask->sid ?>" data-id="<?= $i ?>"
                            style="display: block"
                            href="#component-<?= $widgetTask->sid ?>-<?= $i ?>">
                            <?= $widgetTask->sid ?>.<?= $i + 1 ?>
                        </a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-sm-10">
            <div id="board" class="component-marker-board">
                <img style="max-width: 100%" src="<?= $preview->getImageUrl() ?>">
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs($this->render('show-widget-task-preview.js.php', compact('widgets', 'preview'))) ?>
