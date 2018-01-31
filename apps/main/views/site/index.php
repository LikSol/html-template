<div class="container">
    <div class="jumbotron">
        <h1>Шаблон для верстки</h1>
        <p>Вся суть в компонентах.</p>
    </div>

    <?php foreach ($config['pages'] as $pageSid => $page) :?>
        <h2><?= $page['sid'] ?></h2>
        <div class="row">
            <?php $previews = $page['previews']; \yii\helpers\ArrayHelper::multisort($previews, 'width') ?>
            <?php foreach ($previews as $preview) : ?>
                <div class="col-sm-4 col-md-3">
                    <div class="thumbnail">
                        <div style="height: 200px; overflow: hidden">
                            <a href="<?= \yii\helpers\Url::to(['site/page-preview', 'pageSid' => $page['sid'], 'previewSid' => $preview['sid']]) ?>">
                                <img style="max-width: 100%" src="/data/preview/<?= $preview['file'] ?>">
                            </a>
                        </div>
                        <div class="caption">
                            <h3><?= $page['sid'] ?> <?= $preview['sid']?></h3>
                            <p>Файл: <?= $preview['file'] ?></p>
                            <p>Ширина: <?= $preview['width'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>