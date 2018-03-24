<?php

namespace app\components\widgets;

class BooleanColumn extends \kartik\grid\BooleanColumn
{
    public $trueLabel = "Да";
    public $falseLabel = "Нет";
    
    public $trueIcon = "Да";
    public $falseIcon = "Нет";
    
    public $vAlign = 'middle';
}