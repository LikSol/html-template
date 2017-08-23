<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 23.08.17
 * Time: 14:36
 */
?>
<div class="page__internal__all-pages container">
    <h1>Все страницы</h1>

    <h2>Сайт</h2>

    <ul>
        <?php foreach ($pages['site'] as $page) : ?>
            <li><a href="/template/<?= $page ?>.html"><?= $page ?></a></li>
        <?php endforeach ?>
    </ul>

    <h2>Шаблон</h2>

    <ul>
        <?php foreach ($pages['html-template'] as $page) : ?>
            <li><a href="/template/<?= $page ?>.html"><?= $page ?></a></li>
        <?php endforeach ?>
    </ul>
</div>