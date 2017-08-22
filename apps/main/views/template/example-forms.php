<?php

use main\models\ExampleModel;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

class G {
    /**
     * @var \yii\base\Model
     */
    public static $model;
    /**
     * @var ActiveForm
     */
    public static $form;

    public static function cleanup($field_rendered) {
        $html = preg_replace('/ field-CHANGE-IT-[0-9]+/', '', $field_rendered);
        return $html;
    }

    public static function renderExample($html) {
        $code = static::cleanup($html);
        $code_cleaned_up = preg_replace('/<input type="hidden"[^>]+>/', "", $code);
        $code_cleaned_up = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $code_cleaned_up); // https://stackoverflow.com/a/709684/1775065
        $code_cleaned_up = mb_ereg_replace("([^\n])(</?(input|label))", "\\1\n\\2", $code_cleaned_up);
        return $code . Html::tag('pre', Html::encode($code_cleaned_up));
    }

    public static function id() {
        static $id = 1;

        return 'CHANGE-IT-' . $id++;
    }

    public static function name() {
        static $id = 1;

        return 'CHANGE-IT-TOO-' . $id++;
    }

    public static function textInput() {
        return static::$form->field(static::$model, 'field')->textInput(['id' => static::id(), 'name' => static::name()]);
    }

    public static function checkbox($label = true) {
        return static::$form->field(static::$model, 'field')->checkbox(['uncheck' => null, 'id' => static::id(), 'name' => static::name()], $label);
    }

    public static function fileInput() {
        return static::$form->field(static::$model, 'field')->fileInput(['id' => static::id(), 'name' => static::name()]);
    }

    public static function select() {
        return static::$form->field(static::$model, 'field')->dropDownList([1,2,3], ['id' => static::id(), 'name' => static::name()]);
    }
}

G::$model = new ExampleModel();

?>

<div class="page__example-forms container">
    <h1>Примеры полей ввода</h1>

    <h2>
        Демонстрация
    </h2>

    <div class="demo-buttons">
        <p>
            <button class="btn btn-success" data-object="show-success">Успешный ввод</button>
            К <code>.form-group</code> добавляется класс <code>.has-success</code>.
        </p>
        <p>
            <button class="btn btn-danger" data-object="show-errors">Ошибки ввода</button>
            К <code>.form-group</code> добавляется класс <code>.has-error</code>
            и заполняется <code>div.help-block</code>.
        </p>
        <p>
            <button class="btn" data-object="show-initial">Сбросить</button>
            Сброс всего вышеописанного.
        </p>
    </div>

    <?php G::$form = \yii\widgets\ActiveForm::begin() ?>

    <h2><code>text</code></h2>

    <div class="row forms-example">
        <div class="col-xs-6">
            <h3><code>text</code> с подсказкой и <code>label</code></h3>
            <?= G::renderExample(G::textInput()) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>text</code> с <code>label</code> без подсказки</h3>
            <?= G::renderExample(G::textInput()->hint(false)) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>text</code> с подсказкой без <code>label</code></h3>
            <?= G::renderExample(G::textInput()->label(false)) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>text</code> без <code>label</code> и без подсказки</h3>
            <?= G::renderExample(G::textInput()->label(false)->hint(false)) ?>
        </div>
    </div>

    <h2><code>checkbox</code></h2>

    <div class="row forms-example">
        <div class="col-xs-6">
            <h3><code>checkbox</code> в <code>label</code> с подсказкой</h3>
            <?= G::renderExample(G::checkbox()) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>checkbox</code> вне <code>label</code> с подсказкой</h3>
            <?= G::renderExample(G::checkbox(false)) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>checkbox</code> в <code>label</code> без подсказки</h3>
            <?= G::renderExample(G::checkbox()->hint(false)) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>checkbox</code> вне <code>label</code> без подсказки</h3>
            <?= G::renderExample(G::checkbox(false)->hint(false)) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>checkbox</code> без <code>label</code> c подсказкой</h3>
            <?= G::renderExample(G::checkbox(false)->label(false)) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>checkbox</code> без <code>label</code> без подсказки</h3>
            <?= G::renderExample(G::checkbox(false)->label(false)->hint(false)) ?>
        </div>
    </div>

    <h2><code>file</code></h2>

    <div class="row forms-example">
        <div class="col-xs-6">
            <h3><code>file</code> с подсказкой и <code>label</code></h3>
            <?= G::renderExample(G::fileInput()) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>file</code> с подсказкой без <code>label</code></h3>
            <?= G::renderExample(G::fileInput()->label(false)) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>file</code> без подсказки с <code>label</code></h3>
            <?= G::renderExample(G::fileInput()->hint(false)) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>file</code> без подсказки без <code>label</code></h3>
            <?= G::renderExample(G::fileInput()->hint(false)->label(false)) ?>
        </div>
    </div>

    <h2><code>select</code></h2>

    <div class="row forms-example">
        <div class="col-xs-6">
            <h3><code>select</code> с подсказкой и <code>label</code></h3>
            <?= G::renderExample(G::select()) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>select</code> с подсказкой без <code>label</code></h3>
            <?= G::renderExample(G::select()->label(false)) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>select</code> без подсказки с <code>label</code></h3>
            <?= G::renderExample(G::select()->hint(false)) ?>
        </div>
        <div class="col-xs-6">
            <h3><code>select</code> без подсказки без <code>label</code></h3>
            <?= G::renderExample(G::select()->hint(false)->label(false)) ?>
        </div>
    </div>

    <?php ActiveForm::end() ?>
</div>