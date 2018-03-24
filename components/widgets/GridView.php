<?php

namespace app\components\widgets;

use Yii;
use yii\helpers\Html;
use kartik\grid\GridView as BaseGridView;
use app\components\widgets\LinkPager;
use app\components\widgets\PageSize;

class GridView extends BaseGridView 
{    
    public $id;
    public $dataProvider;
    public $filterModel;

    public $panelType          = 'default';
    public $title              = '';
    public $showPanel          = false;
    public $showTitle          = false;
    public $floatHeader        = false;
    public $canCreate          = true;
    public $urlCreate          = null;
    public $createButtonName   = 'Добавить';
    public $panelAfterCreate   = '';
    public $panelBeforeToolbar = '';
    public $panelAfter         = false;
    public $urlReset           = '';
    
    public $responsive = true;
    public $striped    = true;
    public $bordered   = true;
    public $hover      = true;
    public $condensed  = true;
    
    public $filterSelector = 'select[name="per-page"]';
   
    public function init() {
        if( !isset($this->id) ) {
            $this->id = $this->dataProvider->id;
        }
        
        if( $this->showPanel ) {
        
            $this->panel['type'] = $this->panelType;
            $this->panel['heading'] = ( $this->showTitle ? Html::encode( $this->title ) : false );
            
            $this->panel['before'] = 
                ($this->canCreate
                    ? Html::a('<i class="glyphicon glyphicon-plus"></i> '.$this->createButtonName, 
                              isset($this->urlCreate) ? Yii::$app->urlManager->createUrl([$this->urlCreate]) : ['create'], 
                              ['class' => 'btn btn-success'])
                    : '' 
                ).' '.
                $this->panelAfterCreate;
            
            $this->panel['after'] = $this->panelAfter;            
        }
        
        $this->toolbar = [
            [ 'content' => $this->panelBeforeToolbar ],
            [ 'content' => Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app', 'Reset'), 
                                   $this->urlReset == "" ? ['index'] : Yii::$app->urlManager->createUrl([$this->urlReset]), 
                                   ['class' => 'btn btn-info']) ],
            [ 'content' => PageSize::widget(['gridId' => $this->id]).
                           ( $this->dataProvider->getTotalCount() <= PageSize::getMaxPageSize() ? '{toggleData}' : '' ) ],
            '{export}',
        ];
        
        $this->filterSelector = 'select[name="'.$this->id.'-per-page"]';
        $this->pager['class'] = LinkPager::class;
        
        parent::init();
    }
}
