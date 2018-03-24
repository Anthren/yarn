<?php

namespace app\components\widgets;

use Yii;

class LinkColumn extends \kartik\grid\ActionColumn
{

    public $width = '100px';
    public $hAlign = 'center';

    public static function format($name, $url, $obj, $model) {
        return Html::a($name, Yii::$app->urlManager->createUrl([$url.'index', $obj => $model->id]), [ 'title' => $name, 'class' => 'btn btn-sm btn-link']);
    }

    public function init() {
        $this->header = "Переход";
        $this->template = '';

        foreach ($this->buttons as $key => $value) {
            $this->template .= '{' . $key . '}';
        }

        parent::init();
    }

}
