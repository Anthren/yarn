<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use app\models\YarnKind;

/* @var $this yii\web\View */
/* @var $model app\models\YarnColor */

$this->title = $model->yarnKind->name.' - '.$model->color_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Yarn Colors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="yarn-color-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    DetailView::widget([
        'model' => $model,
        //'condensed' => true,
        'attributes' => [
            [
                'attribute' => 'yarn_kind_id',
                'format' => 'raw',
                'value' => Html::a($model->yarnKind->name,  
                            ['yarn-kind/view', 'id' => $model->yarn_kind_id], 
                            ['title' => 'View yarn kind detail']),
                'type' => DetailView::INPUT_SELECT2,
                'widgetOptions' => [
                    'data' => YarnKind::getList(),
                ],
            ],
            [
                'attribute' => 'color_hex',
                'format' => 'raw',
                'value' => "<span class='circle' style='background-color: {$model->color_hex}'></span> <code>".$model->color_hex.'</code>',
                'type' => DetailView::INPUT_COLOR,
            ],
            'color_name',
        ],
    ])
    ?>
    
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
