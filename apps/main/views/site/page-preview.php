<?php

\main\assets\ComponentMarkerAsset::register($this);

?>

<div class="container">
    <h1>Макет страницы <?= $page['sid'] ?> <?= $preview['sid'] ?></h1>

    <div id="board" class="component-marker-board">
        <img style="max-width: 100%" src="/data/preview/<?= $preview['file'] ?>">
    </div>
</div>

<script type="text/javascript">
    "use strict"
    var ComponentMarker = {}
    ComponentMarker.components = <?= \yii\helpers\Json::encode($components) ?>

    ComponentMarker.boardSelector = '#board'
</script>