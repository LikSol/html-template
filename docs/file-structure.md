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
    └── frontend/
        └── layout/ # стили, js, изображения для общего шаблона
        |   └── layout.css 
        |   └── layout.js
        |   └── logo.svg
        └── ИМЯ_СТРАНИЦЫ/ # стили, js, изображения для конкретной страницы
        |   └── ИМЯ_СТРАНИЦЫ.css 
        |   └── ИМЯ_СТРАНИЦЫ.js
        |   └── illustration.png
        └── component/
            └── components.css # стили для всех компонентов в одном файле 
            └── ИМЯ_КОМПОНЕНТА/ # изображения для конкретного компонента
                └── icon.svg
```

## Пути для общего шаблона (layout)

 * Шаблон: `apps/main/views/layouts/layout.php`
 * CSS: `web/frontend/layout/layout.css` (url: `/frontend/layout/layout.css`)
 * JS: `web/frontend/layout/layout.js` (url: `/frontend/layout/layout.js`)
 * Картинки: `web/frontend/layout/...` (url: `/frontend/layout/...`)

## Пути для шаблона страницы

Ссылка на сайте: `http://имя-сайта/template/ИМЯ_СТРАНИЦЫ.html`.

 * Шаблон: `apps/main/views/template/ИМЯ_СТРАНИЦЫ.php`
 * CSS: `web/frontend/ИМЯ_СТРАНИЦЫ/ИМЯ_СТРАНИЦЫ.css` (url: `/frontend/ИМЯ_СТРАНИЦЫ/ИМЯ_СТРАНИЦЫ.css`)
 * JS: `web/frontend/ИМЯ_СТРАНИЦЫ/ИМЯ_СТРАНИЦЫ.js` (url: `/frontend/ИМЯ_СТРАНИЦЫ/ИМЯ_СТРАНИЦЫ.js`)
 * Картинки: `web/frontend/ИМЯ_СТРАНИЦЫ/...` (url: `/frontend/ИМЯ_СТРАНИЦЫ/...`) 

Пример:

 * Ссылка: `http://имя-сайта/template/about.html`
 * Шаблон: `apps/main/views/template/about.php`
 * CSS: `web/frontend/about/about.css` (url: `/frontend/about/about.css`)
 * JS: `web/frontend/about/about.js` (url: `/frontend/about/about.js`)
 * Картинка: `web/frontend/about/director.png` (url: `/frontend/about/director.png`) 

## Пути для компонентов

Класс компонента: `c__ИМЯ_КОМПОНЕНТА`.

 * CSS: `web/frontend/component/components.css` (url: `/frontend/component/components.css`).
   * (все компоненты в одном css файле)
 * Картинки: `web/frontend/component/ИМЯ_КОМПОНЕНТА/...` (url: `/frontend/component/ИМЯ_КОМПОНЕНТА/...`). 

Пример: 

 * Компонент: `page-header`, класс: `c__page-header`
 * CSS: `web/frontend/component/components.css` (url: `/frontend/component/components.css`) (все компоненты в одном css файле)
 * Картинка: `web/frontend/component/page-header/icon.svg` (url: `/frontend/component/page-header/icon.svg`) 

