<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\ColorInput;
use kartik\widgets\Select2;
use app\models\YarnKind;

/* @var $this yii\web\View */
/* @var $model app\models\YarnColor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="yarn-color-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'yarn_kind_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => YarnKind::all(),
        'options' => ['placeholder' => 'Выберите пряжу...'],
    ]) ?>
    
    <?= $form->field($model, 'color_hex')->widget(ColorInput::classname(), [
        'options' => ['placeholder' => 'Выберите цвет...'],
    ]) ?>

    <?= $form->field($model, 'color_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
