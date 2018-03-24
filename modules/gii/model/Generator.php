<?php

/**
 *
 * @author Ivo Kund <ivo@opus.ee>
 * @date 17.12.13
 */

namespace app\modules\gii\model;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\gii\CodeFile;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;

/**
 * Class Generator
 */
class Generator extends \yii\gii\generators\model\Generator
{
    const RELATIONS_NONE = 'none';
    const RELATIONS_ALL = 'all';
    const RELATIONS_ALL_INVERSE = 'all-inverse';

    public $tableName;
    public $modelClass;
    public $moduleName;
    public $ns = 'app\models';
    public $baseClass = 'app\components\db\ActiveRecord';
    public $relationClassName = 'yii\db\ActiveQuery';
    public $db = 'db';
    public $generateRelations = self::RELATIONS_ALL;
    public $generateLabelsFromComments = true;
    public $enableI18N = true;
    public $useTablePrefix = false;
    public $useSchemaName = true;
    public $generateQuery = false;
    public $queryNs = 'app\models';
    public $queryClass;
    public $queryBaseClass = 'yii\db\ActiveQuery';


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Венец Model Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'Advanced model generator with base classes';
    }
    
    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        // @todo make 'query.php' to be required before 2.1 release
        return ['model.php', 'basemodel.php'/*, 'query.php'*/];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['moduleName'], 'filter', 'filter' => 'trim'],
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
    public function hints()
    {
        return array_merge(parent::hints(), [
            'moduleName' => 'This is the ID of the module that the generated model will belong to.
                If "basic", it means the controller will belong to the application.',
        ]);
    }
    
    /**
     * Checks if module Name is valid
     */
    public function validateModuleName() {
        if( !empty($this->moduleName) && $this->moduleName != 'basic' ) {
            $module = Yii::$app->getModule($this->moduleName);
            if ($module === null) {
                $this->addError('moduleName', "Module '{$this->moduleName}' does not exist.");
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function stickyAttributes() 
    {
        return array_merge(parent::stickyAttributes(), ['moduleName']);
    }
    
    public function generateNamespace() 
    {
        return 'app\\'.( $this->moduleName == '' ? '' : 'modules\\'.$this->moduleName.'\\' ).'models';
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        $namespace = $this->generateNamespace();
        
        foreach ($this->getTableNames() as $tableName) {
            // model :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = $this->generateQuery ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'namespace' => $namespace,
                'relationClassName' => $this->relationClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];
            
            $files[] = new CodeFile(
                    Yii::getAlias('@'.str_replace('\\', '/', $namespace)).'/'.$modelClassName.'.php', 
                    $this->render('model.php', $params)
            );

            $files[] = new CodeFile(
                    Yii::getAlias('@'.str_replace('\\', '/', $namespace)).'/base/'.$modelClassName.'.php', 
                    $this->render('basemodel.php', $params)
            );

            // query :
            if ($queryClassName) {
                $params = [
                    'className' => $queryClassName,
                    'modelClassName' => $modelClassName,
                ];
                $files[] = new CodeFile(
                        Yii::getAlias('@'.str_replace('\\', '/', $namespace)).'/query/'.$queryClassName.'.php', 
                        $this->render('query.php', $params)
                );
            }
        }

        return $files;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    public function generateLabels($table)
    {
        $labels = [];
        foreach ($table->columns as $column) {
            $format = "";
            if (substr($column->comment, 0, 1) === '{') {
                $t = json_decode($column->comment);
                $format = $t->type_column;
            }
            if (true && !empty($column->comment) && ($format === '' )) {
                $labels[$column->name] = $column->comment;
            } elseif (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } elseif ($format === 'list') {
                $labels[$column->name] = $t->label;
            } elseif ($format === 'checkboxList') {
                $labels[$column->name] = $t->label;
            } else {
                $label = Inflector::camel2words($column->name);
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

    /**
     * Generates validation rules for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            }
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
                    $types['safe'][] = $column->name;
                    break;
                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
        }
        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], 'string', 'max' => $length]";
        }

        $db = $this->getDbConnection();

        // Unique indexes rules
        try {
            $uniqueIndexes = $db->getSchema()->findUniqueIndexes($table);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount === 1) {
                        $rules[] = "[['" . $uniqueColumns[0] . "'], 'unique']";
                    } elseif ($attributesCount > 1) {
                        $labels = array_intersect_key($this->generateLabels($table), array_flip($uniqueColumns));
                        $lastLabel = array_pop($labels);
                        $columnsList = implode("', '", $uniqueColumns);
                        $rules[] = "[['$columnsList'], 'unique', 'targetAttribute' => ['$columnsList'], 'message' => 'The combination of " . implode(', ', $labels) . " and $lastLabel has already been taken.']";
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        // Exist rules for foreign keys
        foreach ($table->foreignKeys as $refs) {
            $refTable = $refs[0];
            $refTableSchema = $db->getTableSchema($refTable);
            if ($refTableSchema === null) {
                // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                continue;
            }
            $refClassName = $this->generateClassName($refTable);
            unset($refs[0]);
            $attributes = implode("', '", array_keys($refs));
            $targetAttributes = [];
            foreach ($refs as $key => $value) {
                $targetAttributes[] = "'$key' => '$value'";
            }
            $targetAttributes = implode(', ', $targetAttributes);
            $rules[] = "[['$attributes'], 'exist', 'skipOnError' => true, 'targetClass' => M$refClassName::className(), 'targetAttribute' => [$targetAttributes]]";
        }

        return $rules;
    }

    /**
     * Generates relations using a junction table by adding an extra viaTable().
     * @param \yii\db\TableSchema the table being checked
     * @param array $fks obtained from the checkJunctionTable() method
     * @param array $relations
     * @return array modified $relations
     */
    private function generateManyManyRelations($table, $fks, $relations)
    {
        $db = $this->getDbConnection();

        foreach ($fks as $pair) {
            list($firstKey, $secondKey) = $pair;
            $table0 = $firstKey[0];
            $table1 = $secondKey[0];
            unset($firstKey[0], $secondKey[0]);
            $className0 = $this->generateClassName($table0);
            $className1 = $this->generateClassName($table1);
            $table0Schema = $db->getTableSchema($table0);
            $table1Schema = $db->getTableSchema($table1);

            $link = $this->generateRelationLink(array_flip($secondKey));
            $viaLink = $this->generateRelationLink($firstKey);
            $relationName = $this->generateRelationName($relations, $table0Schema, key($secondKey), true);
            $relations[$table0Schema->fullName][$relationName] = [
                "return \$this->hasMany(M$className1::className(), $link)->viaTable('".$this->generateTableName($table->name)."', $viaLink);",
                $className1,
                true,
                $table1
            ];

            $link = $this->generateRelationLink(array_flip($firstKey));
            $viaLink = $this->generateRelationLink($secondKey);
            $relationName = $this->generateRelationName($relations, $table1Schema, key($firstKey), true);
            $relations[$table1Schema->fullName][$relationName] = [
                "return \$this->hasMany(M$className0::className(), $link)->viaTable('".$this->generateTableName($table->name)."', $viaLink);",
                $className0,
                true,
                $table0
            ];
        }

        return $relations;
    }

    /**
     * @return array the generated relation declarations
     */
    protected function generateRelations()
    {
        if ($this->generateRelations === self::RELATIONS_NONE) {
            return [];
        }

        $db = $this->getDbConnection();

        $relations = [];
        foreach ($this->getSchemaNames() as $schemaName) {
            foreach ($db->getSchema()->getTableSchemas($schemaName) as $table) {
                $className = $this->generateClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    $refTableSchema = $db->getTableSchema($refTable);
                    if ($refTableSchema === null) {
                        // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                        continue;
                    }
                    unset($refs[0]);
                    $fks = array_keys($refs);
                    $refClassName = $this->generateClassName($refTable);

                    // Add relation for this table
                    $link = $this->generateRelationLink(array_flip($refs));
                    $relationName = $this->generateRelationName($relations, $table, $fks[0], false);
                    $relations[$table->fullName][$relationName] = [
                        "return \$this->hasOne(M$refClassName::className(), $link);",
                        $refClassName,
                        false,
                        $refTable
                    ];

                    // Add relation for the referenced table
                    $hasMany = $this->isHasManyRelation($table, $fks);
                    $link = $this->generateRelationLink($refs);
                    $relationName = $this->generateRelationName($relations, $refTableSchema, $className, $hasMany);
                    $relations[$refTableSchema->fullName][$relationName] = [
                        "return \$this->" . ($hasMany ? 'hasMany' : 'hasOne') . "(M$className::className(), $link);",
                        $className,
                        $hasMany,
                        $table->fullName
                    ];
                }

                if (($junctionFks = $this->checkJunctionTable($table)) === false) {
                    continue;
                }

                $relations = $this->generateManyManyRelations($table, $junctionFks, $relations);
            }
        }

        if ($this->generateRelations === self::RELATIONS_ALL_INVERSE) {
            return $this->addInverseRelations($relations);
        }

        return $relations;
    }

    /**
     * Generate a relation name for the specified table and a base name.
     * @param array $relations the relations being generated currently.
     * @param \yii\db\TableSchema $table the table schema
     * @param string $key a base name that the relation name may be generated from
     * @param boolean $multiple whether this is a has-many relation
     * @return string the relation name
     */
    protected function generateRelationName($relations, $table, $key, $multiple)
    {
        if (!empty($key) && substr_compare($key, 'id', -2, 2, true) === 0 && strcasecmp($key, 'id')) {
            $key = rtrim(substr($key, 0, -2), '_');
        }
        if ($multiple) {
            $key = Inflector::pluralize($key);
        }
        $name = $rawName = Inflector::id2camel($key, '_');
        $i = 0;
        while (isset($table->columns[lcfirst($name)])) {
            $name = $rawName . ($i++);
        }
        while (isset($relations[$table->fullName][$name])) {
            $name = $rawName . ($i++);
        }

        return $name;
    }

    protected $tableNames;
    protected $classNames;
}
