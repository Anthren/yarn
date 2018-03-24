<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass;
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use app\components\widgets\datecontrol\DateControl;
use app\components\widgets\datecontrol\TimeControl;
use kartik\checkbox\CheckboxX;

/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">
    <?= "<?php" ?> $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); ?>
    <?= "<?=" ?> Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [ 
<?php foreach ($safeAttributes as $attribute) {
                echo $generator->generateActiveField($attribute)." \n";
} ?>
            ]
        ]); 
    ?>
    
    <br>
    <?= "<?=" ?> Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Вернуться', ['index'], [
        'class' => 'btn btn-primary', 
        'title'=> 'Вернуться к списку'
    ]) ?>
    <div class="pull-right">        
        <?= "<?=" ?> property_exists($model, 'supportDraft')
            ? Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> Сохранить как черновик', [
                'class' => 'btn btn-info',
                'name'  => 'action',
                'value' => 'save-draft',
            ]) : '' ?>
        <?= "<?=" ?> $model->isNewRecord
            ? Html::submitButton('<i class="glyphicon glyphicon-plus"></i> Создать', [
                'class' => 'btn btn-success',
                'name'  => 'action',
                'value' => 'save-operation',
            ]) 
            : Html::submitButton('<i class="glyphicon glyphicon-plus"></i> Обновить', [
                'class' => 'btn btn-primary',
            ]) ?>
    </div>
    
    <?= "<?php" ?> ActiveForm::end(); ?>
</div>