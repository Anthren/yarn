<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\ColorInput;
use kartik\widgets\Select2;
use app\models\YarnKind;

/* @var $this yii\web\View */
/* @var $model app\models\YarnColor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="yarn-color-form">

    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL]); ?>
    
    <?= $form->field($model, 'yarn_kind_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => YarnKind::getList(),
        'options' => ['placeholder' => 'Выберите пряжу...'],
    ]) ?>
    
    <?= $form->field($model, 'color_hex')->widget(ColorInput::classname(), [
        'options' => ['placeholder' => 'Выберите цвет...'],
    ]) ?>

    <?= $form->field($model, 'color_name')->textInput(['maxlength' => true]) ?>
    
    <br>
    <div class="form-group">
        <?= Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Вернуться', ['index'], [
            'class' => 'btn btn-primary',
            'title' => 'Вернуться'
        ]) ?>
        <div class="pull-right">
            <?= Yii::$app->controller->action->id == 'update'
                ? Html::a('<i class="glyphicon glyphicon-eye-open"></i> Просмотр', ['view', 'id' => $model->id], ['class' => 'btn btn-info']) 
                : '' ?>

            <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> '.Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
