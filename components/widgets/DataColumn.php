<?php

namespace app\components\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\components\widgets\GridView;

class DataColumn extends \kartik\grid\DataColumn
{
    public $width = '200px';
    public $vAlign = 'middle';
    
    public $contentOptions = ['class' => 'kartik-sheet-style'];
    public $headerOptions  = ['class' => 'kartik-sheet-style'];
    
    public $source;
    public $fieldShowType;

    const TYPE_LIST = 'list';
    const TYPE_LIST_NO_VALUE = 'list_no_value';
    const TYPE_LIST_TEXT_FILTER = 'list_text_filter';    
    const TYPE_DECIMAL2 = 'decimal2';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_ACCOUNT = 'account';
    const TYPE_CHECKBOX_LIST = 'checkboxList';
    const TYPE_DATETIME = 'datetime';
    const TYPE_DATETIME_RANGE = 'datetime_range';
    
    private function preinit() 
    {
        if( $this->fieldShowType === self::TYPE_LIST ) {
            $this->filter = ( !isset($this->filter) ? $this->source : $this->filter );
            $this->source = $this->source + [ null => null ];
            $this->value = function ($model, $key, $index, $column) {
                return $this->source[ArrayHelper::getValue($model, $column->attribute)];
            };
        } elseif( $this->fieldShowType === self::TYPE_LIST_TEXT_FILTER ) {
            $this->filterType = GridView::FILTER_SELECT2;
            $this->filter = [ null => null ] + $this->source;
            $this->source = [ null => null ] + $this->source;
            $this->value = function ($model, $key, $index, $column) {
                return $this->source[ArrayHelper::getValue($model, $column->attribute)];
            };
        } elseif( $this->fieldShowType === self::TYPE_LIST_NO_VALUE ) {
            $this->filter = $this->source;
            $this->source = $this->source + [ null => null ];
        } elseif( $this->fieldShowType === self::TYPE_CHECKBOX_LIST ) {
            $this->filter = $this->source;
            $this->value = function ($model, $key, $index, $column) {
                return $this->source[ArrayHelper::getValue($model, $column->attribute)];
            };
            
        } elseif( $this->fieldShowType === self::TYPE_DECIMAL ) {
            $this->hAlign = 'right';
            $this->format = [ 'decimal', 4 ];
            $this->noWrap = true;
        } elseif( $this->fieldShowType === self::TYPE_DECIMAL2 ) {
            $this->hAlign = 'right';
            $this->format = [ 'decimal', 2 ];
            $this->noWrap = true;
            
        } elseif( $this->fieldShowType === self::TYPE_ACCOUNT ) {
            $this->hAlign = 'center';
            $this->width = '200px';
            
        } elseif( $this->fieldShowType === GridView::FILTER_DATE ) {
            $this->format = ['date', 'php:d.m.Y']; //['date', Yii::$app->modules['datecontrol']['displaySettings']['date']];
            $this->filterType = GridView::FILTER_DATE;
            $this->filterWidgetOptions = [
                'language' => 'ru',
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                ]
            ];       
            $this->hAlign = 'center';        
        } elseif( $this->fieldShowType === GridView::FILTER_DATE_RANGE ) {
            $this->format = ['date', 'php:d.m.Y']; //['date', Yii::$app->modules['datecontrol']['displaySettings']['date']];
            $this->filterType = GridView::FILTER_DATE_RANGE;
            $this->filterWidgetOptions = [
                'language' => 'ru',
                'hideInput' => true,
                'presetDropdown' => true,
                'convertFormat' => true,
                'pluginOptions' => [
                    'locale' => [
                        'format' => 'd.m.Y',
                    ],
                    'autoclose' => 'true',
                ]
            ];
            $this->hAlign = 'center';
        } elseif( $this->fieldShowType === self::TYPE_DATETIME || $this->fieldShowType === GridView::FILTER_DATETIME ) {
            //$this->format = ['date', 'php:d.m.Y H:i:s']; //['date', Yii::$app->modules['datecontrol']['displaySettings']['datetime']];
            $this->filterType = GridView::FILTER_DATE;
            $this->filterWidgetOptions = [
                'language' => 'ru',
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                ]
            ];    
            $this->hAlign = 'center';    
        }
         elseif( $this->fieldShowType === GridView::FILTER_TIME ) {
            $this->format = ['date', Yii::$app->modules['datecontrol']['displaySettings']['time']];
            $this->hAlign = 'center';
        }
    }

    public function init()
    {
        self::preinit();
        parent::init();
    }
    
    public function renderDataCell($model, $key, $index)
    {
        $options = $this->fetchContentOptions($model, $key, $index);
        $this->parseGrouping($options, $model, $key, $index);
        $this->parseExcelFormats($options, $model, $key, $index);
        $this->initPjax($this->_clientScript);
        $dataFormat = $this->format;
        switch (gettype($dataFormat)) {
            case 'string':
                $options['class'] .= ' export-type-'.$dataFormat;
                break;
            case 'array':
                $options['class'] .= ' export-type-'.$dataFormat[0];
                break;
        }
        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }

}
