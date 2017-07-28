# Фиксированная структура HTML некоторых элементов

## Подключение CSS и JS

 * CSS подключаются в `<head>`.
 * JS подключаются перед закрывающим тегом `</body>`.
 
Указанное выше шаблон делает сам.

 * Инлайновые стили/скрипты (через `<style>...</style>`/`<script>...</script`) не допускаются.

## Контейнер

Для определения ширины контента и отступов по краям необходимо
 использовать **только** bootstrap'овские `.container` или `.container-fluid`.

```html
<div class="container">адаптивный контейнер</div>
<div class="container-fluid">резиновый контейнер</div>
```

## Кнопки

Необходимо использовать кнопки bootstrap.

Обычно - в виде ссылок:

```html
<a class="btn" href="...">...</a>

```

В формах - в виде `button`:

```html
<button class="btn">...</button>
```

## Input'ы в формах

1. Необходимо использовать bootstrap'овские input'ы с приведенной
 ниже структурой.
   * Структура может быть скорректирована по согласованию.
2. Необходимо проверять вид input'а, добавляя к `.form-group` класс
`has-error`, например: `<div class="form-group has-error">...</div>`. 
Это эмулирует ситуацию, когда посетитель на сайте ввел в форму 
 неверное значение - должно отобразиться сообщение об ошибке.

Допустимые изменения:

 * Добавление произвольного класса к `.form-group`.
 * Текст внутри (**не html**, а только текст).
 * `id` у `input`, `for` у `label`.
 * Указание `placeholder`. 
 * Корректирование структуры - по согласованию.

### type="text"

```html
<div class="form-group">
    <label class="control-label" for="input-id">Название</label>
    <input type="text" id="input-id" class="form-control">
    <div class="help-block">сообщение об ошибке</div>
</div>
```

### type="checkbox"

```html
<div class="form-group">
    <label>
        <input type="checkbox" id="input-id"> Название
    </label>
    <div class="help-block">сообщение об ошибке</div>
</div>
```

### type="file"

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

### Прочие 

По согласованию.