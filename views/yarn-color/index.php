<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\models\YarnKind;
/* @var $this yii\web\View */
/* @var $searchModel app\models\YarnColorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Yarn Colors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="yarn-color-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Yarn Color'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'yarn_kind_id',
                'class' => '\kartik\grid\DataColumn',
                'value' => function ($model, $key, $index, $widget) { 
                    return Html::a($model->yarnKind->name,  
                        ['yarn-kind/view', 'id' => $model->yarn_kind_id], 
                        ['title' => 'View yarn kind detail']);
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => YarnKind::all(), 
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Все'],
                'format' => 'raw',
                'width' => '30%',
            ],
            [
                'attribute' => 'color_hex',
                'class' => '\kartik\grid\DataColumn',
                'value' => function ($model, $key, $index, $widget) {
                    return "<span class='badge' style='background-color: {$model->color_hex}'> </span> <code>".$model->color_hex.'</code>';
                },
                'width' => '10%',
                'filterType' => GridView::FILTER_COLOR,
                'vAlign' => 'middle',
                'format' => 'raw',
            ],
            'color_name',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
