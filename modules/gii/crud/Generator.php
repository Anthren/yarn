<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\gii\crud;

use Yii;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use yii\web\Controller;

/**
 * Generates CRUD
 *
 * @property array $columnNames Model column names. This property is read-only.
 * @property string $controllerID The controller ID (without the module ID prefix). This property is
 * read-only.
 * @property array $searchAttributes Searchable attributes. This property is read-only.
 * @property boolean|\yii\db\TableSchema $tableSchema This property is read-only.
 * @property string $viewPath The controller view path. This property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\generators\crud\Generator {

    public $modelClass = 'app\models\Post';
    public $moduleName;
    public $controllerClass = 'app\controllers\PostController';

    public $viewPath;
    public $baseControllerClass = 'app\components\web\Controller';
    public $indexWidgetType = 'grid';
    public $searchModelClass = 'app\models\PostSearch';
    public $enableI18N = true;
    public $messageCategory = 'app';


    /**
     * @inheritdoc
     */
    public function getName() 
    {
        return 'Венец CRUD Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates a controller and views that implement CRUD (Create, Read, Update, Delete)
            operations for the specified data model.';
    }

    /**
     * @inheritdoc
     */
    public function rules() 
    {
        return array_merge(parent::rules(), [
            [['moduleName', 'baseControllerClass'], 'filter', 'filter' => 'trim'],
            [['moduleName'], 'validateModuleName'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() 
    {
        return array_merge(parent::attributeLabels(), [
            'moduleName' => 'Module Name',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints() {
        return array_merge(parent::hints(), [
            'moduleName' => 'This is the ID of the module that the generated controller will belong to.
                If "basic", it means the controller will belong to the application.',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['controller.php'];
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes() 
    {
        return array_merge(parent::stickyAttributes(), ['baseControllerClass', 'moduleName', 'indexWidgetType']);
    }

    /**
     * Checks if model class is valid
     */
    public function validateModelClass()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pk = $class::primaryKey();
        if (empty($pk)) {
            $this->addError('modelClass', "The table associated with $class must have primary key(s).");
        }
    }

    /**
     * Checks if model ID is valid
     */
    public function validateModuleName() {
        if (!empty($this->moduleName)) {
            $module = Yii::$app->getModule($this->moduleName);
            if ($module === null) {
                $this->addError('moduleName', "Module '{$this->moduleName}' does not exist.");
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $controllerFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php');

        $files = [
            new CodeFile($controllerFile, $this->render('controller.php')),
        ];

        if (!empty($this->searchModelClass)) {
            $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
            $files[] = new CodeFile($searchModel, $this->render('search.php'));
        }

        $viewPath = $this->getViewPath();
        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $file) {
            if (empty($this->searchModelClass) && $file === '_search.php') {
                continue;
            }
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }

        return $files;
    }

    /**
     * @return string the controller ID (without the module ID prefix)
     */
    public function getControllerID()
    {
        $pos = strrpos($this->controllerClass, '\\');
        $class = substr(substr($this->controllerClass, $pos + 1), 0, -10);

        return Inflector::camel2id($class);
    }

    /**
     * @return string the controller view path
     */
    public function getViewPath() 
    {
        $module = empty($this->moduleName) ? Yii::$app : Yii::$app->getModule($this->moduleName);
        return $module->getViewPath() . '/' . $this->getControllerID();
    }

    public function getNameAttribute()
    {
        foreach ($this->getColumnNames() as $name) {
            if (!strcasecmp($name, 'name') || !strcasecmp($name, 'title')) {
                return $name;
            }
        }
        /* @var $class \yii\db\ActiveRecord */
        $class = $this->modelClass;
        $pk = $class::primaryKey();

        return $pk[0];
    }

    /**
     * Generates code for active field
     * @param string $attribute
     * @return string
     */
    public function generateActiveField($attribute) 
    {
        $model = new $this->modelClass();
        $attributeLabels = $model->attributeLabels();
        $tableSchema = $this->getTableSchema();
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return 
"                '$attribute' => [
                    'type' => TabularForm::INPUT_PASSWORD, 
                    'options' => [ 
                        'placeholder' => 'Введите " . $attributeLabels[$attribute] . "...'
                    ]
                ],";
                //return "\$form->field(\$model, '$attribute')->passwordInput()";
            } else {
                return 
"                '$attribute' => [
                    'type' => TabularForm::INPUT_TEXT, 
                    'options' => [
                        'placeholder' => 'Введите " . $attributeLabels[$attribute] . "...'
                    ]
                ],";
                //return "\$form->field(\$model, '$attribute')";
            }
        }
        $column = $tableSchema->columns[$attribute];

        if (substr($column->comment, 0, 1) === '{') {
            $t = json_decode($column->comment);
            if (!property_exists($t, 'id')) { $t->id = 'id';  }
            if ($t->type_column === 'list') {
                return
"                '$attribute' => [
                    'type' => Form::INPUT_DROPDOWN_LIST,
                    'items' => " . $t->model . "::getList()
                ],";
            }
        }
        
        if ($column->phpType === 'boolean') {
            //return "\$form->field(\$model, '$attribute')->checkbox()";
            return 
"                '$attribute' => [
                    'type' => Form::INPUT_WIDGET,
                    'widgetClass' => CheckboxX::classname(),
                    'options' => [ 
                        'pluginOptions' => [ 
                            'threeState' => false,
                            'size' => 'xl',
                        ],
                    ],
                ],";
        } elseif ($column->dbType === 'tinyint(1)') {
            return 
"                '$attribute' => [
                    'type' => Form::INPUT_WIDGET,
                    'widgetClass' => CheckboxX::classname(),
                    'options' => [ 
                        'pluginOptions' => [ 
                            'threeState' => false,
                            'size' => 'xl',
                        ],
                    ],
                ],";
            
        } elseif ($column->type === 'text') {
            //return "\$form->field(\$model, '$attribute')->textarea(['rows' => 6])";
            return 
"                '$attribute' => [
                    'type' => Form::INPUT_TEXTAREA, 
                    'options' => [
                        'placeholder' => 'Введите " . $attributeLabels[$attribute] . "...',
                        'rows' => 6
                    ]
                ],";
            
        } elseif ($column->type === 'date') {
            return 
"                '$attribute' => [
                    'type' => Form::INPUT_WIDGET, 
                    'widgetClass' => DateControl::classname()
                ],";
        } elseif ($column->type === 'time') {
            return 
"                '$attribute' => [
                    'type' => Form::INPUT_WIDGET, 
                    'widgetClass' => TimeControl::classname()
                ],";
        } elseif ($column->type === 'datetime' || $column->type === 'timestamp') {
            return 
"                '$attribute' => [
                    'type' => Form::INPUT_WIDGET,
                    'widgetClass' => DateControl::classname(),
                    'options' => [
                        'type' => DateControl::FORMAT_DATETIME
                    ]
                ],";
            
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'INPUT_PASSWORD';
            } else {
                $input = 'INPUT_TEXT';
            }
            if ($column->phpType !== 'string' || $column->size === null) {
                //return "\$form->field(\$model, '$attribute')->$input()";
                return 
"                '$attribute' => [
                    'type' => Form::" . $input . ",
                    'options' => [
                        'placeholder' => 'Введите " . $attributeLabels[$attribute] . "...'
                    ]
                ],";
            } else {
                //return "\$form->field(\$model, '$attribute')->$input(['maxlength' => $column->size])";
                return 
"                '$attribute' => [
                    'type' => Form::" . $input . ",
                    'options' => [
                        'placeholder' => 'Введите " . $attributeLabels[$attribute] . "...',
                        'maxlength' => " . $column->size . "
                    ]
                ],";
            }
        }
    }

    /**
     * Generates code for active search field
     * @param string $attribute
     * @return string
     */
    public function generateActiveSearchField($attribute)
    {
        $tableSchema = $this->getTableSchema();
        if ($tableSchema === false) {
            return "\$form->field(\$model, '$attribute')";
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->phpType === 'boolean') {
            return "\$form->field(\$model, '$attribute')->checkbox()";
        } else {
            return "\$form->field(\$model, '$attribute')";
        }
    }

    /**
     * Generates column format
     * @param \yii\db\ColumnSchema $column
     * @return string
     */
    public function generateColumnFormat($column) 
    {
        if (substr($column->comment, 0, 1) === '{') {
            $t = json_decode($column->comment);
            return $t->type_column;
        }

        if ($column->phpType === 'boolean') {
            return 'boolean';
        } elseif ($column->type === 'text') {
            return 'ntext';
        } elseif (stripos($column->name, 'time') !== false && $column->phpType === 'integer') {
            return 'datetime';
        } elseif (stripos($column->name, 'email') !== false) {
            return 'email';
        } elseif (stripos($column->name, 'url') !== false) {
            return 'url';
        } else {
            return 'text';
        }
    }

    /**
     * Generates validation rules for the search model.
     * @return array the generated validation rules
     */
    public function generateSearchRules()
    {
        if (($table = $this->getTableSchema()) === false) {
            return ["[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"];
        }
        $types = [];
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                default:
                    $types['safe'][] = $column->name;
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }

        return $rules;
    }

    /**
     * @return array searchable attributes
     */
    public function getSearchAttributes()
    {
        return $this->getColumnNames();
    }

    /**
     * Generates the attribute labels for the search model.
     * @return array the generated attribute labels (name => label)
     */
    public function generateSearchLabels()
    {
        /* @var $model \yii\base\Model */
        $model = new $this->modelClass();
        $attributeLabels = $model->attributeLabels();
        $labels = [];
        foreach ($this->getColumnNames() as $name) {
            if (isset($attributeLabels[$name])) {
                $labels[$name] = $attributeLabels[$name];
            } else {
                if (!strcasecmp($name, 'id')) {
                    $labels[$name] = 'ID';
                } else {
                    $label = Inflector::camel2words($name);
                    if (strcasecmp(substr($label, -3), ' id') === 0) {
                        $label = substr($label, 0, -3) . ' ID';
                    }
                    $labels[$name] = $label;
                }
            }
        }

        return $labels;
    }

    /**
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions()
    {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->modelClass;
            /* @var $model \yii\base\Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "'{$column}' => \$this->{$column},";
                    break;
                case Schema::TYPE_TIME:
                    $hashConditions[] = "'{$column}' => TimeConverter::convertToPhysical(\$this->{$column}),";
                    break;                
                case Schema::TYPE_DATE:
                    $hashConditions[] = "'{$column}' => DateConverter::convertToPhysical(\$this->{$column}),";
                    break;                
                default:
                    $likeConditions[] = "->andFilterWhere(['like', '{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                    . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                    . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

    /**
     * Generates URL parameters
     * @return string
     */
    public function generateUrlParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                return "'id' => (string)\$model->{$pks[0]}";
            } else {
                return "'id' => \$model->{$pks[0]}";
            }
        } else {
            $params = [];
            foreach ($pks as $pk) {
                if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                    $params[] = "'$pk' => (string)\$model->$pk";
                } else {
                    $params[] = "'$pk' => \$model->$pk";
                }
            }

            return implode(', ', $params);
        }
    }

    /**
     * Generates action parameters
     * @return string
     */
    public function generateActionParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            return '$id';
        } else {
            return '$' . implode(', $', $pks);
        }
    }

    /**
     * Generates parameter tags for phpdoc
     * @return array parameter tags for phpdoc
     */
    public function generateActionParamComments()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (($table = $this->getTableSchema()) === false) {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . (substr(strtolower($pk), -2) == 'id' ? 'integer' : 'string') . ' $' . $pk;
            }

            return $params;
        }
        if (count($pks) === 1) {
            return ['@param ' . $table->columns[$pks[0]]->phpType . ' $id'];
        } else {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . $table->columns[$pk]->phpType . ' $' . $pk;
            }

            return $params;
        }
    }

    /**
     * Returns table schema for current model class or false if it is not an active record
     * @return boolean|\yii\db\TableSchema
     */
    public function getTableSchema()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema();
        } else {
            return false;
        }
    }

    /**
     * @return array model column names
     */
    public function getColumnNames()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema()->getColumnNames();
        } else {
            /* @var $model \yii\base\Model */
            $model = new $class();

            return $model->attributes();
        }
    }
}
