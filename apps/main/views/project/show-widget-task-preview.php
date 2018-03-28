<?php

\main\assets\ComponentMarkerAsset::register($this);

/**
 * @var $widgets \main\models\WidgetTask[]
 * @var $preview \main\models\DesignPreview
 * @var $this \yii\web\View
 */
?>
<style>
    .appearance-list .heading {
        margin-top: 5px;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .appearance-list .list .appearanceItem {
        display: block;
    }
    .appearance-list .list {
        margin-left: 10px;
    }
    .componentDetailLink {
        margin-left: 10px;
        float: right;
    }
</style>

<div class="container">
    <h1>Разметка страницы <?= $preview->design->sid ?>.<?= $preview->sid ?></h1>

    <div class="row" data-object="cut-board-workspace">
        <div class="col-sm-2">
            <div style="position: fixed; z-index: 3;" class="appearance-list" data-property="appearance-list">
            </div>
        </div>
        <div class="col-sm-10 preview-cut-workspace">
            <div class="previews" data-property="previews">
                <div data-object="board" data-qid="<?= $preview->qid ?>" class="board">
                    <div class="controls" data-property="controls">
                        <a href="#" data-property="size-original"><></a>
                        <a href="#" data-property="size-plus">+</a>
                        <a href="#" data-property="size-minus">-</a>
                    </div>
                    <div data-property="preview" class="preview component-marker-board">
                        <img src="<?= $preview->getImageUrl() ?>"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs($this->render('show-widget-task-preview.js.twig', ['widgetTasks' => $widgets, 'preview' => $preview])) ?>
