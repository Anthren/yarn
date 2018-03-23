<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "yarn_color".
 *
 * @property int $id
 * @property int $yarn_kind_id Вид
 * @property string $color_hex Код цвета
 * @property string $color_name Название цвета
 *
 * @property YarnKind $yarnKind
 */
class YarnColor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yarn_color';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['yarn_kind_id', 'color_hex', 'color_name'], 'required'],
            [['yarn_kind_id'], 'integer'],
            [['color_hex'], 'string', 'max' => 7],
            [['color_name'], 'string', 'max' => 255],
            [['yarn_kind_id'], 'exist', 'skipOnError' => true, 'targetClass' => YarnKind::className(), 'targetAttribute' => ['yarn_kind_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'yarn_kind_id' => Yii::t('app', 'Вид'),
            'color_hex' => Yii::t('app', 'Код цвета'),
            'color_name' => Yii::t('app', 'Название цвета'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getYarnKind()
    {
        return $this->hasOne(YarnKind::className(), ['id' => 'yarn_kind_id']);
    }
}
