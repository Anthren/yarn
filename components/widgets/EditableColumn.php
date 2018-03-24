<?php

namespace app\components\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use kartik\editable\Editable;
use app\components\widgets\GridView;

/**
 * Description of DateConverter
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class EditableColumn extends \kartik\grid\EditableColumn
{

    public $width = '200px';
    public $vAlign = 'middle';
    public $source;
    public $fieldShowType;
    public $editableHeader = '';

    const TYPE_LIST = 'list';
    const TYPE_MONEY = 'money';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_OFFICE = 'office';
    const TYPE_ACCOUNT = 'account';
    const TYPE_CHECKBOX_LIST = 'checkboxList';

    public function init()
    {
        if( $this->fieldShowType === self::TYPE_LIST ) {
            $this->filter = $this->source;
            $this->source = $this->source + [ null => null ];
            $this->value = function ($model, $key, $index, $column) {
                return $this->source[ArrayHelper::getValue($model, $column->attribute)];
            };
            $this->editableOptions = [
                'header' => $this->editableHeader,
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data' => $this->source,
            ];
        } elseif( $this->fieldShowType === self::TYPE_CHECKBOX_LIST ) {
            $this->filter = $this->source;
            $this->value = function ($model, $key, $index, $column) {
                return $this->source[ArrayHelper::getValue($model, $column->attribute)];
            };
                
        } elseif( $this->fieldShowType === self::TYPE_DECIMAL ) {
            $this->hAlign = 'right';
            $this->format = [ 'decimal', 4 ];
            
        } elseif( $this->fieldShowType === self::TYPE_MONEY ) {
            $this->hAlign = 'right';
            $this->format = [ 'decimal', 2 ];
            $this->pageSummary = true;
            $this->refreshGrid = true;
            $this->editableOptions = [
                'header' => $this->editableHeader,
                'inputType' => Editable::INPUT_SPIN,
                'options' => [
                    'pluginOptions' => [ 'min' => 0 ]
                ],
            ];
            
        } elseif( $this->fieldShowType === self::TYPE_ACCOUNT ) {
            $this->hAlign = 'center';
            $this->width = '200px';
            
        } elseif( $this->fieldShowType === GridView::FILTER_DATE ) {
            $this->filterType = GridView::FILTER_DATE;
            $this->format = ['date', Yii::$app->modules['datecontrol']['displaySettings']['date']];
            $this->filterWidgetOptions = [
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                ]
            ];
            $this->width = '150px';           
            $this->hAlign = 'center';        
        } elseif( $this->fieldShowType === GridView::FILTER_DATE_RANGE ) {
            $this->filterType = GridView::FILTER_DATE_RANGE;
            $this->format = ['date', Yii::$app->modules['datecontrol']['displaySettings']['date']];
            //$this->label = "Период";
            $this->filterWidgetOptions = [
                'pluginOptions' => [
                    'format' => 'DD.MM.YYYY',                    
                    'autoclose' => 'true',
                    'separator' => ' - '
                ]
            ];
            $this->width = '180px';
            $this->hAlign = 'center';        
        }/* elseif( $this->fieldShowType === GridView::FILTER_TIME ) {
            $this->filterType = GridView::FILTER_TIME;
            $this->format = ['date', Yii::$app->modules['datecontrol']['displaySettings']['time']];
            $this->width = '150px';
            $this->hAlign = 'center';
        }*/
        
        parent::init();
    }

}
