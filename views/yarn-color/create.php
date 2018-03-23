<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\YarnColor */

$this->title = Yii::t('app', 'Create Yarn Color');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Yarn Colors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="yarn-color-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
