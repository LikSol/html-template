## Страницы

### Кратко

 * Страницы располагаются в `projects/имя_проекта/src/pages/имя_страницы/имя_страницы.html.twig`
 * CSS и JS подключаются автоматически, располагаются рядом с файлом страницы и должны назваться
 `имя_страницы.css` или `имя_страницы.js` соответственно.
 * Картинки должны располагаться в папке страницы или в подпапках, и подключаются стандартно 
 (относительной ссылкой, например `<img src="image.png">`).
 * Можно использовать Twig 1.x.
 
В целом это практически то же, что обычная верстка в файлах, только не надо подключать css/js и можно 
использовать шаблонизатор twig.

Далее то же самое подробнее.
 
### Расположение файлов страниц 

Страницы располагаются в папках вида `projects/имя_проекта/src/pages/имя_страницы/`.
Сам файл страницы может называться как угодно, но его расширение должно быть `.html.twig`.
    
Например, имя этой страницы `features`, соответственно, файл этой страницы находится по пути:
    
 * `projects/demo/src/pages/features/features.html.twig`
    
Список всех страниц можно посмотреть, перейдя на страницу проекта. Как только будет создан новый файл страницы,
он автоматически появится в списке.

### Twig

При верстке страниц можно использовать шаблонизатор [twig 1.x](https://twig.symfony.com/doc/1.x/). 

### CSS страницы

CSS страницы должен располагаться по пути вида `projects/имя_проекта/src/pages/имя_страницы/имя_страницы.css`.
Он подключится автоматически, как только будет создан такой файл. 

Например, CSS-файл этой страницы находится по пути:

 * `projects/demo/src/pages/features/features.css`

### JS страницы

JS страницы подключаются аналогично CSS. Например, JS 
для <button data-object="demo-page-features-js-button">этой кнопки</button>
располагается в `pages/features/features.js`.

Файлы JS такжже подключаются автоматически.

### Изображения

Изображения должны находиться в одной папке со страницей или глубже.

<img style="max-width: 100px; display: block;" src="features-images/stamp.png">

Например, файл картинки, расположенной выше, находится по пути:

 * `projects/demo/src/pages/features/features-images/stamp.png`
 
И подключается как:  

```
<img src="features-images/stamp.png">
```

То есть, просто относительно текущей страницы.

### Изображения в CSS

В css также можно использовать изображения, которые размещены в папке страницы, по относительным
ссылкам. Например:

<div class="demoBackground">CSS background</div>

На примере выше для фона используется изображение `progress.png`, которое находится рядом с `features.css` и подключается
в CSS как:

```
background: url('progress.png'); 
```