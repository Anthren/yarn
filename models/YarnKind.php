<?php

namespace app\models;

use yii\helpers\ArrayHelper;

class YarnKind extends base\YarnKind
{
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            // add additional translations
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            // add additional rules
        ]);
    }
    
    public static function getList() {
        return ArrayHelper::map(YarnKind::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }
}

