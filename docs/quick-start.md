# Быстрое начало работы

Установите шаблон по [инструкции](install.md).

Откройте главную страницу по пути `http://имя-сайта/template/index.html`.

Откроется пример главной страницы с небольшим количеством тестового
контента. 

## Общий принцип

Движок берет общий шаблон (layout), вставляет в него шаблон 
запрошенной страницы и выводит в браузер.

К каждой странице автоматически подключаются:

 * Библиотеки
   * Jquery
   * Bootstrap
 * CSS
   * `/frontend/component/components.css` 
   * `/frontend/layout/layout.css`
   * `/frontend/ИМЯ_СТРАНИЦЫ/ИМЯ_СТРАНИЦЫ.css` (если существует)
 * JS
   * `/frontend/layout/layout.js`
   * `/frontend/ИМЯ_СТРАНИЦЫ/ИМЯ_СТРАНИЦЫ.js`  (если существует)

При необходимости можно добавить дополнительные библиотеки 
JS/CSS (см. ниже).

## Создание основного шаблона (layout)

Отредактируйте файлы основного шаблона для создания шапки и футера:

 * HTML: `apps/main/views/layouts/layout.php`
 * CSS: `web/frontend/layout/layout.css`
 * JS: `web/frontend/layout/layout.js`
 * Картинки: `web/frontend/layout/...`
 
Руководствуйтесь инструкциями и требованиями в файлах.

## Создание главной страницы (index)

Страница доступна по ссылке `http://имя-сайта/template/index.html`.

Отредактируйте файлы главной страницы:

 * HTML: `apps/main/views/template/index.php`
 * CSS: `web/frontend/index/index.css`
 * JS: `web/frontend/index/index.js`

Размещайте картинки страницы в соответствующей папке:
 
 * Картинки: `web/frontend/index/...`
 
Руководствуйтесь инструкциями и требованиями в файлах.

## Создание других страниц

Придумайте имя страницы. Например, '**about**'. Страница будет 
доступна по ссылке `http://имя-сайта/template/`**about**`.html`. 

Создайте копии файлов:

 * HTML: `apps/main/views/template/example.php` -> **about.php**
 * CSS: `web/frontend/example/example.css` -> `web/frontend/`**about**`/`**about**`.css`
 * JS: `web/frontend/example/example.js` -> `web/frontend/`**about**`/`**about**`.js`

Размещайте картинки страницы в соответствующей папке:

 * Картинки: `web/frontend/`**about**`/...`
 
Отредактируйте файлы для заполнения страницы.

Руководствуйтесь инструкциями и требованиями в файлах.

## Подключение библиотек JS/CSS для страницы/шаблона

Руководствуйтесь [требованиями по работе со сторонними библиотеками](external-libraries.md).

Для каждой страницы автоматически подключаются файлы 
`/frontend/ИМЯ_СТРАНИЦЫ/ИМЯ_СТРАНИЦЫ.css` и `/frontend/ИМЯ_СТРАНИЦЫ/ИМЯ_СТРАНИЦЫ.js` 
(если они существуют).

Дополнительно можно подключить библиотеки с CDN.

Для этого нужно в файле шаблона html (например, 
в `apps/main/views/template/index.php` - для главной страницы) 
добавить блок вида:

```php
<?php
// Карусель slick
$assets->addCss('https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css');
$assets->addCss('https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css');
$assets->addJs('https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js');
?>

```

То же самое можно сделать в файле основного шаблона `layout.php`,
если библиотеки требуются не конкретной странице, а общему шаблону.

Порядок вывода CSS/JS на странице описан [здесь](other-rules.md).

## Соблюдайте требования

[Требования к верстке](README.md).