<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
<?php
if ($generator->indexWidgetType === 'grid') {
    echo 'use app\components\widgets\GridView;';
} elseif ($generator->indexWidgetType === 'list') {
    echo 'use yii\\widgets\\ListView;';
} elseif ($generator->indexWidgetType === 'dyna') {
    echo 'use app\components\widgets\DynaGrid;';
}
?>

use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
<?= !empty($generator->searchModelClass) ? " * @var " . ltrim($generator->searchModelClass, '\\') . " \$searchModel\n" : '' ?>
 */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

<?php if (substr($generator->indexWidgetType,0,4) === 'grid'): ?>
    <?= "<?php  echo " ?>GridView::widget([
        'title' => $this->title,
        'canCreate' => $canCreate,
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
            [
                'class' => 'app\components\widgets\SerialColumn',
            ],
<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 26) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach( $tableSchema->columns as $column ) {
        $format = $generator->generateColumnFormat($column);
        
        if( $column->name == 'id' ) { continue; }
        
        if( $column->type === 'date' ) {
            $columnDisplay = "            [
                'class' => 'app\components\widgets\DataColumn',
                'attribute' => '$column->name',
                'fieldShowType' => GridView::FILTER_DATE,
            ],";
        } elseif( $column->type === 'time' ) {
            $columnDisplay = "            [
                'class' => 'app\components\widgets\DataColumn',
                'attribute' => '$column->name',
                'fieldShowType' => GridView::FILTER_TIME,
            ],";
        } elseif( $column->type === 'datetime' || $column->type === 'timestamp' ){
            $columnDisplay = "            [
                'attribute' => '$column->name',
                'format' => ['datetime', Yii::\$app->modules['datecontrol']['displaySettings']['datetime']],
                'hAlign' => 'center',
            ],";
        
        } elseif( $column->type === 'decimal' ){
            $columnDisplay = "            [
                'class' => 'app\components\widgets\DataColumn',
                'attribute' => '$column->name',".
                ( $column->scale == 4 ? "
                'fieldShowType' => \app\components\widgets\DataColumn::TYPE_DECIMAL," : "
                'fieldShowType' => \app\components\widgets\DataColumn::TYPE_MONEY," )."
            ],";
        
        } elseif( $column->dbType === 'tinyint(1)' ) {
            $columnDisplay = "            [
                'class' => '\app\components\widgets\BooleanColumn',
                'attribute' => '$column->name',
            ],"  ;
        
        } elseif( $format === 'list' ) {
            $t = json_decode($column->comment);
            if( !property_exists($t, 'id') ) { $t->id = 'id'; }
            $columnDisplay = "            [
                'class' => 'app\components\widgets\DataColumn',
                'attribute' => '$column->name',".
                ( $column->name == 'office_id' ? "
                'fieldShowType' => app\components\widgets\DataColumn::TYPE_OFFICE," : "
                'fieldShowType' => app\components\widgets\DataColumn::TYPE_LIST," )."
                'source' => ".$t->model."::getList(),
                'width' => '250px',
            ],"  ;
        
        } elseif( $format !== 'text' ) {    
            $columnDisplay = "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',";
        
        } else {
            $columnDisplay = "            [
                'class' => 'app\components\widgets\DataColumn',
                'attribute' => '$column->name',
                'width' => '200px',
            ],";
        }
        if( ++$count < 26 ) {
            echo $columnDisplay ."\n";
        } else {
            echo "/*" . $columnDisplay . " \n */";
        }
    }
}
?>
            [
                'class' => 'app\components\widgets\ActionColumn',
                'canDelete' => $canDelete,
                'canUpdate' => $canUpdate,
                'url' => '<?= ($generator->moduleName == '' ? '' : $generator->moduleName.'/' ).$generator->controllerID ?>/',
            ],
        ],
    ]);  ?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>
</div>
