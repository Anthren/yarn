<?php

namespace app\models;

use app\models\base\YarnColor as BaseYarnColor;

class YarnColor extends BaseYarnColor
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
        return ArrayHelper::map(YarnKind::find()->all(), 'id', 'color_name');
    }
    
    function getTitle() {
        return $this->yarnKind->name.' - '.$this->color_name;
    }
}