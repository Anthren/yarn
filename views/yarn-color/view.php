<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\YarnColor */

$this->title = $model->yarnKind->name.' - '.$model->color_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Yarn Colors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="yarn-color-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        /*'striped' => false,
        'condensed' => true,*/
        'attributes' => [
            [
                'attribute' => 'yarn_kind_id',
                'format' => 'raw',
                'value' => Html::a($model->yarnKind->name,  
                            ['yarn-kind/view', 'id' => $model->yarn_kind_id], 
                            ['title' => 'View yarn kind detail']),
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

</div>
