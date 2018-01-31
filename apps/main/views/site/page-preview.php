<?php

\main\assets\ComponentMarkerAsset::register($this);

/**
 * @var \main\models\DataConfig $dataConfig
 */

$components = $dataConfig->getComponentsForPagePreview($page, $preview);
?>

<div class="container">
    <h1>Макет страницы <?= $page['sid'] ?> <?= $preview['sid'] ?></h1>

    <div class="row">
        <div class="col-sm-2">
            <div id="controls" style="position: fixed;">
                <?php foreach ($components as $component) : ?>
                    <a
                        data-type="component" data-sid="<?= $component->sid ?>"
                        style="display: block"
                        href="<?= \yii\helpers\Url::to(['site/component-about', 'sid' => $component->sid]) ?>">
                        <?= $component['sid'] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-sm-10">
            <div id="board" class="component-marker-board">
                <img style="max-width: 100%" src="/data/preview/<?= $preview['file'] ?>">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict"
    var ComponentMarker = {}
    ComponentMarker.components = <?=
        \yii\helpers\Json::encode(\yii\helpers\ArrayHelper::map(
            $components, 'sid', function ($component) use ($page, $preview) {
                return $component->getAppearanceForPagePreview($page, $preview);
        })
        )
    ?>

    ComponentMarker.boardSelector = '#board'
    ComponentMarker.controlsSelector = '#controls'
</script>