<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\YarnKind */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Yarn Kinds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="yarn-kind-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'hook',
        ],
    ]) ?>

    <div>
        <div class="pull-left">
            <?= Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Вернуться', ['index'], ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="pull-right">
            <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
            <?= Html::a('<i class="glyphicon glyphicon-trash"></i> Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger', 
                'data-method' => 'post', 
                'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?')
            ]) ?>
        </div>
        <div class="clearfix"></div>
        <br>
    </div>
    
</div>
