<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use app\components\widgets\datecontrol\DateControl;
use app\components\widgets\datecontrol\TimeControl;
<?php if (($generator->indexWidgetType) === 'gridSettings'): ?>
use app\components\widgets\DetailViewSettings as DetailView;
<?php else: ?>
use app\components\widgets\DetailView;
<?php endif; ?>

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">
    <?= "<?= " ?>DetailView::widget([
        'model' => $model,
        'title' => $this->title,
        'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "            '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {

        $format = $generator->generateColumnFormat($column);
        
        if( $column->name == 'id' ) { continue; }

        if($column->type === 'date'){
            echo "            [
                'attribute' => '$column->name',
                'format' => [ 'date', Yii::\$app->modules['datecontrol']['displaySettings']['date'] ],
                'type' => DetailView::INPUT_WIDGET,
                'widgetOptions' => [
                    'class' => DateControl::classname(),
                ]
            ],\n";

        }elseif($column->type === 'time'){
            echo "            [
                'attribute' => '$column->name',
                'format' => [ 'time', Yii::\$app->modules['datecontrol']['displaySettings']['time'] ],
                'type' => DetailView::INPUT_WIDGET,
                'widgetOptions' => [
                    'class' => TimeControl::classname(),
                ]
            ],\n";
        }elseif($column->type === 'datetime' || $column->type === 'timestamp'){
            echo "            [
                'attribute' => '$column->name',
                'format' => [ 'datetime', Yii::\$app->modules['datecontrol']['displaySettings']['datetime'] ],
                'type' => DetailView::INPUT_WIDGET,
                'widgetOptions' => [
                    'class' => DateControl::classname(),
                    'type' => DateControl::FORMAT_DATETIME
                ]
            ],\n";
        }elseif($format === 'list'){
                 $t = json_decode($column->comment);
                 if (!property_exists($t, 'id')) { $t->id = 'id';  }
                 $arr2 = explode("\\", $t->model);
            echo "            [
                'attribute' => '$column->name',
                'items' => ".$t->model."::getList(),
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'value' => ".'$model->'.lcfirst ($arr2[ count ($arr2)-1]).( ($t->id == 'id')?"":( ucfirst($t->id) )) .'->'.$t->field.", 
            ],\n";            
            
        }elseif($format === 'boolean'){
                 $t = json_decode($column->comment);
                 if (!property_exists($t, 'id')) { $t->id = 'id';  }
                 $arr2 = explode("\\", $t->model);
            echo "            [
                'attribute' => '$column->name',
                'format' => 'boolean',
                'type' => DetailView::INPUT_CHECKBOX_X,
                'widgetOptions' => [
                    'pluginOptions' => [ 
                        'threeState' => false,
                        'size' => 'lg',
                    ]
                ]
            ],\n";            
            
        }else{
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
        ],
        'enableEditMode' => $canUpdate,
        'canDelete' => $canDelete,
    ]) ?>
</div>
