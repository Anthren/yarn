<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\YarnKind */

$this->title = Yii::t('app', 'Create Yarn Kind');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Yarn Kinds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="yarn-kind-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
