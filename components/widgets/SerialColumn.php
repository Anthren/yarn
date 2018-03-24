<?php

namespace app\components\widgets;
                                       
class SerialColumn extends \kartik\grid\SerialColumn {

    public $hAlign = 'right';
    public $header = '№';
    public $width = '50px';
    
    public function init() {
        parent::init();
    }

}
