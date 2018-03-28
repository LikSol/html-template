<?php

/** @var $this \yii\web\View */
?>
<?php
\cronfy\yii2Assets\BootstrapCdnAsset::register($this);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $this->title ?: 'Html template' ?></title>

    <?= \yii\helpers\Html::csrfMetaTags() ?>

    <?php $this->head() ?>

</head>
<body>
<?php $this->beginBody() ?>

<?= $content; ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
