<?php

namespace app\components\widgets;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
                                       
class SplitButtonDropdown extends \yii\base\Widget {

    public $label = '';
    public $url = '';
    public $options = [];
    public $dropdown;
    public $type = 'default';
    public $align = 'left';
    
    public function run() {
        $aClass = 'a-'.$this->type;
        $buttonClass = 'btn btn-'.$this->type;
        
        $dropdownMenu = '';
        $dropdownItems = $this->dropdown['items'];
        foreach( $dropdownItems as $dropdownItem ) {
            $label = ArrayHelper::getValue( $dropdownItem, 'label', '' );
            $url = ArrayHelper::getValue( $dropdownItem, 'url', '' );
            $options = ArrayHelper::getValue( $dropdownItem, 'options', [] );
            $dropdownMenu .= '<li>'.Html::a( $label, $url, $options + ['class' => $aClass] ).'</li>';
        }
        
        $output = 
            '<div class="btn-group">'.
                Html::a( $this->label, $this->url, $this->options + ['class' => $buttonClass]).
                '<button type="button" class="'.$buttonClass.' dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>'.
                '<ul class="dropdown-menu pull-'.$this->align.'">'.
                    $dropdownMenu.
                '</ul>
            </div>';
        
        return $output;
    }

}
