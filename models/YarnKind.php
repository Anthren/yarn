<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use app\models\base\YarnKind as BaseYarnKind;

class YarnKind extends BaseYarnKind
{
    function all() {
        return ArrayHelper::map(YarnKind::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }
}

