# Структура файлов

## Общая структура

```
├── apps/
|   └── main/
|       └── views/
|           └── layouts/
|           |   └── layout.php # общий шаблон
|           └── template/
|               └── ИМЯ_СТРАНИЦЫ.php # шаблон конкретной страницы
└── web/ # document root веб-сервера
    └── css/
    |   └── layout.css # стили общего шаблона
    |   └── ИМЯ_СТРАНИЦЫ.css # стили конкретной страницы
    |   └── components.css # стили компонентов
    └── js/
    |   └── layout.js # скрипты общего шаблона
    |   └── ИМЯ_СТРАНИЦЫ.js # скрипты конкретной страницы
    └── image/
        └── layout/
        |   └── *.(png|jpg|...) # изображения общего шаблона
        └── ИМЯ_СТРАНИЦЫ/
        |   └── *.(png|jpg|...) # изображения конкретной страницы
        └── component/
            └── ИМЯ_КОМПОНЕНТА/
                └── *.(png|jpg|...) # изображения компонента
```

## Пути для общего шаблона (layout)

 * Шаблон: `apps/main/views/layouts/layout.php`
 * CSS: `web/css/layout.css` (url: `/css/layout.css`)
 * JS: `web/js/layout.js` (url: `/js/layout.js`)
 * Картинки: `web/image/layout/...` (url: `/image/layout/...`)

## Пути для шаблона страницы

Ссылка на сайте: `http://имя-сайта/template/ИМЯ_СТРАНИЦЫ.html`.

 * Шаблон: `apps/main/views/template/ИМЯ_СТРАНИЦЫ.php`
 * CSS: `web/css/ИМЯ_СТРАНИЦЫ.css` (url: `/css/ИМЯ_СТРАНИЦЫ.css`)
 * JS: `web/js/ИМЯ_СТРАНИЦЫ.js` (url: `/js/ИМЯ_СТРАНИЦЫ.js`)
 * Картинки: `web/image/ИМЯ_СТРАНИЦЫ/...` (url: `/image/ИМЯ_СТРАНИЦЫ/...`) 

Пример:

 * Ссылка: `http://имя-сайта/template/about.html`
 * Шаблон: `apps/main/views/template/about.php`
 * CSS: `web/css/about.css` (url: `/css/about.css`)
 * JS: `web/js/about.js` (url: `/js/about.js`)
 * Картинка: `web/image/about/director.png` (url: `/image/about/director.png`) 

## Пути для компонентов

Класс компонента: `c__ИМЯ_КОМПОНЕНТА`.

 * CSS: `web/css/components.css` (url: `/css/components.css`).
   * (все компоненты в одном css файле)
 * Картинки: `web/image/component/ИМЯ_КОМПОНЕНТА/...` (url: `/image/component/ИМЯ_КОМПОНЕНТА/...`). 

Пример: 

 * Компонент: `page-header`, класс: `c__page-header`
 * CSS: `web/css/components.css` (url: `/css/components.css`) (все компоненты в одном css файле)
 * Картинка: `web/image/component/page-header/icon.svg` (url: `/image/component/page-header/icon.svg`) 

