<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.17
 * Time: 17:48
 */

namespace main\models;

use yii\base\Model;

class ExampleModel extends Model
{
    public $field;

    public function attributeLabels()
    {
        return [
            'field' => 'Название поля'
        ];
    }

    public function attributeHints()
    {
        return [
            'field' => 'подсказка'
        ];
    }
}