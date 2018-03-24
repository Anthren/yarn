<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;

/* @var $this yii\web\View */
/* @var $model app\models\YarnKind */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="yarn-kind-form">
    
    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL]); ?>
    
    <?= Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'name' => [
                    'type' => Form::INPUT_TEXT,
                    'options' => [
                        'maxlength' => 255
                    ]
                ],
                'hook' => [
                    'type' => Form::INPUT_TEXT,
                    'options' => [
                        'maxlength' => 4
                    ]
                ],
            ]
        ]);
    ?>

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
