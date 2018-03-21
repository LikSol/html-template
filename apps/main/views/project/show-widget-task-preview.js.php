<?php

\main\assets\ComponentMarkerAsset::register($this);

/**
 * @var $widgets \main\models\WidgetTask[]
 * @var $preview \main\models\DesignPreview
 * @var $this \yii\web\View
 */

$components = [];
foreach ($widgets as $widgetTask) {
    $components[$widgetTask->sid] = $widgetTask->getPreviewCoords($preview);
}
?>

"use strict"

ComponentMarker.run({
    boardSelector: '#board',
    controlsSelector: '#controls',
    components: <?= \yii\helpers\Json::encode($components)?>
})
<?php //=
//        \yii\helpers\Json::encode(\yii\helpers\ArrayHelper::map(
//            $components, 'sid', function ($component) use ($page, $preview) {
//                return $component->getAppearanceForPagePreview($page, $preview);
//        })
//        )
//    ?>

