<?php

namespace app\models;

use app\models\base\YarnColor as BaseYarnColor;

class YarnColor extends BaseYarnColor
{
    function getTitle() {
        return $this->yarnKind->name.' - '.$this->color_name;
    }
}