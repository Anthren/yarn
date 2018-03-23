<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = Yii::t('app', 'Yarns');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<ul>
<?php foreach ($yarns as $yarn): ?>
    <li>
        <?= Html::encode("{$yarn->name}") ?>
    </li>
<?php endforeach; ?>
</ul>

<?= LinkPager::widget(['pagination' => $pagination]) ?>

<p>
    <?= Html::a(Yii::t('app', 'Yarn Kinds'), ['yarn-kind/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Yarn Colors'), ['yarn-color/index'], ['class' => 'btn btn-primary']) ?>
</p>

