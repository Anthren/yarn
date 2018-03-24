<?php

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;

/**
 * This is the template for generating a CRUD controller class file.
 *
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/** @var ActiveRecordInterface $class */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use app\components\helpers\utilHelper;
use app\components\helpers\dateHelper;
use app\components\helpers\messageHelper;
use yii\filters\VerbFilter;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex()
    {
<?php $path = ( $generator->moduleName == '' ? '' : $generator->moduleName.'/' ) . $generator->controllerID; ?>
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $canUpdate = \Yii::$app->user->can('/<?= $path ?>/update');
        $canDelete = \Yii::$app->user->can('/<?= $path ?>/delete');
        $canCreate = \Yii::$app->user->can('/<?= $path ?>/create');
       
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'canUpdate' => $canUpdate,
            'canDelete' => $canDelete,
            'canCreate' => $canCreate,
        ]);
<?php else: ?>
        $canUpdate = \Yii::$app->user->can('/<?= $path ?>/update');
        $canDelete = \Yii::$app->user->can('/<?= $path ?>/delete');
        $canCreate = \Yii::$app->user->can('/<?= $path ?>/create');
        
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'canUpdate' => $canUpdate,
            'canDelete' => $canDelete,
            'canCreate' => $canCreate,
        ]);
<?php endif; ?>
    }

    /**
     * Displays a single <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionView(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);
        $canUpdate = \Yii::$app->user->can('/<?=  ($generator->moduleName ==''?'':  $generator->moduleName .'/' ). $generator->controllerID  ?>/update');
        $canDelete = \Yii::$app->user->can('/<?=  ($generator->moduleName ==''?'':  $generator->moduleName .'/' ). $generator->controllerID  ?>/delete');
        
        if ($canUpdate) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model-><?=$generator->getTableSchema()->primaryKey[0]?>]);
            }
        }    
            
        return $this->render('view', [
            'model' => $model,
            'canUpdate' => $canUpdate, 
            'canDelete' => $canDelete, 
        ]);

    }
    
    /**
     * Creates a new <?= $modelClass ?> model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new <?= $modelClass ?>;
<?php 
$model = new $generator->modelClass;
$safeAttributes = $model->safeAttributes();

foreach ($safeAttributes as $attribute) {
    if (stripos($attribute, 'datetime') !== false) {
        echo '        $model->'.$attribute . ' = Yii::$app->formatter->asDatetime(dateHelper::timeUTC());'." \n";
    }elseif(stripos($attribute, 'date') !== false) {
        echo '        $model->'.$attribute . ' = Yii::$app->formatter->asDate(dateHelper::timeUTC());'." \n";
    }elseif (stripos($attribute, 'time') !== false) {
        echo '        $model->'.$attribute . ' = Yii::$app->formatter->asTime(dateHelper::timeUTC());'." \n";
    }
} ?>
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', <?= $urlParams ?>]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /** для доступа только
     */
    public function actionUpdate() {}

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionDelete(<?= $actionParams ?>)
    {
        $this->findModel(<?= $actionParams ?>)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?=                   $modelClass ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        } else {
            utilHelper::notAllowedAction('Данная страница не существует или у Вас нет доступа');
        }
    }
}
