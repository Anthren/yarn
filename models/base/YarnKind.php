<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "yarn_kind".
 *
 * @property int $id
 * @property string $name Наименование
 * @property string $hook Размер крючка
 *
 * @property YarnColor[] $yarnColors
 */
class YarnKind extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yarn_kind';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'hook'], 'required'],
            [['hook'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Наименование'),
            'hook' => Yii::t('app', 'Размер крючка'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getYarnColors()
    {
        return $this->hasMany(YarnColor::className(), ['yarn_kind_id' => 'id']);
    }
}
