# Фиксированная структура HTML некоторых элементов

## Подключение CSS и JS

 * CSS подключаются в `<head>`.
 * JS подключаются перед закрывающим тегом `</body>`.
 
Указанное выше шаблон делает сам.

 * Инлайновые стили/скрипты (через `<style>...</style>`/`<script>...</script>`) не допускаются.

## Контейнер

Для определения ширины контента и отступов по краям необходимо
 использовать **только** bootstrap'овские `.container` или `.container-fluid`.
  
Класс контейнера размещается на первом или втором уровне от `<body>`.

На первом уровне:

```html
<div class="container page__SOME-NAME">адаптивный контейнер</div>
<div class="container-fluid page__SOME-NAME">резиновый контейнер</div>
```

На втором уровне:

```html
<div class="page__SOME-NAME">
    <div class="container">адаптивный контейнер</div>
</div>
<div class="page__SOME-NAME">
    <div class="container-fluid">резиновый контейнер</div>
</div>
```


## Кнопки

Кнопкой является любой небольшой прямоугольный элемент в рамочке (с или без фона), внутри которого находится текст и/или иконка
и на который можно кликнуть.

Необходимо использовать кнопки bootstrap, тег `<button>`:

```html
обычная
<button class="btn">...</button>

блоковая
<button class="btn btn-block">...</button>
```

Использование ссылок (тег `<a>`) для кнопок не допускается.

## Поля ввода (`input`, `select` и т. д.)

Требования должны соблюдаться для абсолютно всех полей ввода (в формах и вне форм),
для которых приведены примеры ниже.

1. Необходимо использовать bootstrap'овские поля ввода с приведенной
 ниже структурой.
   * Структура может быть скорректирована по согласованию.
2. Необходимо проверять вид поля ввода, добавляя к `.form-group` класс
`has-error`, например: `<div class="form-group has-error">...</div>`. 
Это эмулирует ситуацию, когда посетитель на сайте ввел в форму 
 неверное значение - должно отобразиться сообщение об ошибке.

Допустимые изменения:

 * Добавление произвольного класса к `.form-group`.
 * Текст внутри (**не html**, а только текст).
 * `id` у `input`, `for` у `label`.
 * Указание `placeholder`. 
 * Корректирование структуры - по согласованию.

Примеры полей ввода можно посмотреть в развернутом шаблоне по ссылке `/template/example-forms.html`.

### input type="text"

```html
<div class="form-group">
    <label class="control-label" for="input-id">Название</label>
    <input type="text" id="input-id" class="form-control">
    <div class="help-block">сообщение об ошибке</div>
</div>
```

### input type="checkbox"

```html
<div class="form-group">
    <label>
        <input type="checkbox" id="input-id"> Название
    </label>
    <div class="help-block">сообщение об ошибке</div>
</div>
```

### input type="file"

```html
<div class="form-group">
    <label class="control-label" for="input-id">Название</label>
    <input type="file" id="input-id">
    <div class="help-block">сообщение об ошибке</div>
</div>
```

### select

```html
<div class="form-group">
    <label class="control-label" for="input-id">Название</label>
    <select id="input-id" class="form-control">
        <option value="1">вариант 1</option>
    </select>
    <div class="help-block">сообщение об ошибке</div>
</div>
```

### И т. д. 

Примеры всех полей ввода можно посмотреть в развернутом шаблоне по ссылке `/template/example-forms.html`.

Изменения возможны по предварительному согласованию.

## Почему нужно использовать только такой HTML?

Это необходимо для совместимости с другими компонентами и фреймворками. Если будет использоваться
другая структура, верстку придется переделывать при установке на движок сайта.