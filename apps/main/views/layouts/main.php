<?php

/**
 *
 *
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 * ЭТОТ ФАЙЛ НЕ РЕДАКТИРУЕМ
 *
 *
 */
/** @var $this \yii\web\View */
?>
<?php
\main\assets\MandatoryAsset::register($this);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $this->title ?: 'Html template' ?></title>

    <?php $this->head() ?>

</head>
<body>
<?php $this->beginBody() ?>

<?php
    /** @var \main\components\AutoAssets $assets */
    $assets = Yii::$app->autoAssets;
    $assets->mode = $assets::MODE_LAYOUT;
    include "layout.php";
    // после layout.php, иначе не будут досупны ассеты из layout.php
    \main\assets\PageAsset::register($this);
    // до endBody(), иначе не сработает
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
