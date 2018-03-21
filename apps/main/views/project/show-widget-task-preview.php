<?php

\main\assets\ComponentMarkerAsset::register($this);

/**
 * @var $widgetTask \main\models\WidgetTask
 * @var $preview \main\models\DesignPreview
 * @var $this \yii\web\View
 */
?>

<div class="container">
    <h1>Виджет <?= $widgetTask->sid ?> на странице <?= $preview->design->sid ?>.<?= $preview->sid ?></h1>

    <div class="row">
        <div class="col-sm-2">
            <div id="controls" style="position: fixed;">
                <?php foreach ($widgetTask->getPreviewCoords($preview) as $i => $coords) : ?>
                    <a
                        data-type="component" data-sid="<?= $widgetTask->sid ?>" data-id="<?= $i ?>"
                        style="display: block"
                        href="#component-<?= $widgetTask->sid ?>-<?= $i ?>">
                        <?= $i + 1 ?>
                    </a>
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

<?php $this->registerJs($this->render('show-widget-task-preview.js.php', compact('widgetTask', 'preview'))) ?>
