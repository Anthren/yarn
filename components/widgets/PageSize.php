<?php

namespace app\components\widgets;

use yii\helpers\Html;

class PageSize extends \yii\base\Widget
{
    public static $defaultPageSize = 15;
    public static $sizes = [10 => 10,  20 => 20, 50 => 50, 100 => 100, 500 => 500, 1000 => 1000];
    
    public $label = '';
    public $pageSizeParam = 'per-page';    
    public $template = '{list} {label}';
    public $options;
    public $labelOptions;
    public $encodeLabel = true;
    public $gridId = 'per';
    
    public static function getDefaultPageSize() {
        return self::$defaultPageSize;
    }
    
    public static function getMaxPageSize() {
        return max( self::$sizes );
    }
    
    public static function getLimitsPageSizes() {
        return [ 1, self::getMaxPageSize() ];
    }

    public function run() {
        if (empty($this->options['id'])) {
            $this->options['id'] = $this->id;
        }
        if (empty($this->options['class'])) {
            $this->options['class'] = 'btn btn-default';
        }
        if ($this->encodeLabel) {
            $this->label = Html::encode($this->label);
        }
        
        $this->pageSizeParam = $this->gridId.'-'.$this->pageSizeParam;
        $perPage = !empty($_GET[$this->pageSizeParam]) ? $_GET[$this->pageSizeParam] : self::$defaultPageSize;

        $listHtml = Html::dropDownList($this->gridId.'-per-page', $perPage, self::$sizes, $this->options);
        $labelHtml = Html::label($this->label, $this->options['id'], $this->labelOptions);

        $output = str_replace(['{list}', '{label}'], [$listHtml, $labelHtml], $this->template);

        return $output;
    }

}
