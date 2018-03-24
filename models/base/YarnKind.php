<?php

namespace app\models\base;

/**
 * This is the base model class for table "yarn_kind".
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
            'id' => 'ID',
            'name' => 'Наименование',
            'hook' => 'Размер крючка',
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
