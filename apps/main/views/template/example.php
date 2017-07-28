<?php
/**
 * Главное:
 *      https://github.com/cronfy/html-template/blob/master/docs/README.md
 *
 * О шаблонах HTML:
 *      Расположение CSS, JS, картинок:
 *      https://github.com/cronfy/html-template/blob/master/docs/file-structure.md
 *      Требования к именованию классов и структуре CSS:
 *      https://github.com/cronfy/html-template/blob/master/docs/css-structure-and-naming.md
 *      Фиксированная структура для некоторых элементов HTML:
 *      https://github.com/cronfy/html-template/blob/master/docs/fixed-html.md
 *
 * Стандартная страница сайта.
 *
 * Для повторяющихся на разных страницах элементов - h1, хлебные крошки, пагинация и пр. - можно
 * использовать общий файл стилей `web/frontend/component/components.css`, чтобы избежать дублирования кода.
 *
 */
?>
<div class="page__example">
    <div class="container">
        <div class="c__page-header">
            <!-- это компонент из components.css -->
            <h1>Заголовок страницы (компонент) (с картинкой в css)</h1>
        </div>
        Пример стандартной страницы - блок в адаптивном контейнере.
        <p>Картинка на этой странице, находится в <code>web/frontend/example/...</code>,
            url будет <code>/frontend/example/...</code>
            <img src="/frontend/example/stamp.png" style="width: 200px;"></p>
    </div>
    <div class="container-fluid">
        Пример стандартной страницы - блок во всю ширину.
    </div>
</div>

