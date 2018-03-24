<?php

namespace app\components\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

                                       
class ActionColumn extends \kartik\grid\ActionColumn {

    public $width = '100px';
    public $hAlign = 'center';
    
    public $template;
    public $canView = true;
    public $canUpdate = true;
    public $canDelete = true;
    public $moreButtons = false;
    
    public $url = '';
    public $askDelete = 'Вы уверены, что хотите удалить этот элемент?';
    public $idField = 'id';
            
    public function init() 
    {
        if( !isset($this->template) ) {
            $this->template = 
                ( $this->canView     ? '{view}'   : '' ).
                ( $this->canUpdate   ? '{update}' : '' ).
                ( $this->canDelete   ? '{delete}' : '' ).
                ( $this->moreButtons ? '{more}'   : '' );
        }
        
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model) {
                return Html::a( '<i class="glyphicon glyphicon-eye-open"></i>', 
                                Url::toRoute([$this->url.'view', 'id' => $model->{$this->idField}]), 
                                ['title' => Yii::t('app', 'View'), 'class' => 'btn btn-sm btn-link'] );
            };
        }
        
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model) { 
                return Html::a( '<i class="glyphicon glyphicon-pencil"></i>', 
                                Url::toRoute([$this->url.'update', 'id' => $model->{$this->idField}]), 
                                ['title' => Yii::t('app', 'Edit'), 'class' => 'btn btn-sm btn-link'] );
                return Html::a( '<i class="glyphicon glyphicon-pencil"></i>', 
                                Url::toRoute([$this->url.'view', 'id' => $model->{$this->idField}, 'edit' => 't']), 
                                ['title' => Yii::t('app', 'Edit'), 'class' => 'btn btn-sm btn-link'] );
            };
        }
        
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model) { 
                return Html::a('<i class="glyphicon glyphicon-trash"></i>', 
                            Url::toRoute([$this->url.'delete', 'id' => $model->{$this->idField}]), 
                            ['title' => Yii::t('app', 'Delete'), 'class' => 'btn btn-sm btn-link', 
                             'data-method' => 'post', 'data-confirm' => $this->askDelete] );
            };
        }
        
        if (!isset($this->buttons['more'])) {
            $this->buttons['more'] = $this->moreButtons;
        }
        
        parent::init();
    }

}
