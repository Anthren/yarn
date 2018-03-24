<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use app\components\widgets\DataColumn;
use app\components\widgets\GridView;
//use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\YarnKindSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Yarn Kinds');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="yarn-kind-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Create Yarn Kind'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <br>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'app\components\widgets\SerialColumn',
            ],
            [
                'class' => 'app\components\widgets\DataColumn',
                'attribute' => 'name',
                'width' => '400px'
            ],
            [
                'class' => 'app\components\widgets\DataColumn',
                'attribute' => 'hook',
                'fieldShowType' => DataColumn::TYPE_DECIMAL2,
            ],
            [
                'class' => 'app\components\widgets\ActionColumn',
                'url' => 'yarn-kind/',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
