<?php

namespace app\components\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\helpers\Html;
use kartik\detail\DetailView as BaseDetailView;


class DetailView extends BaseDetailView {
    
    public $canComeBack = true;
    public $defaultPrevPage = 'index';
    
    public $canDelete = true;
    public $askDelete = 'Вы уверены, что хотите удалить этот элемент?';
    public $askUpdate = null;
    
    public $panelType = DetailView::TYPE_DEFAULT;
    public $condensed = false;
    public $hover = true;
    
    public $title = "";
    public $useId;
    public $useDV = 'w0';
    
    public $moreButtonsL = false;
    public $moreButtonsR = false;

    protected function renderPanel($content) 
    {
        $panel = $this->panel;
        $type = ArrayHelper::remove($panel, 'type', self::TYPE_DEFAULT);
        $panel['heading'] = ArrayHelper::getValue($panel, 'heading', '');
        $panel['preBody'] = $content;
        return Html::panel($panel, $type);
    }

    public function init() 
    {
        $this->panel['type'] = $this->panelType; 
        $this->panel['heading'] = $this->title;
        
        $this->mode = Yii::$app->request->get('edit') == 't' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW;
        if( !$this->enableEditMode ) {
            $this->mode = DetailView::MODE_VIEW;
        }
        $this->useDV = Yii::$app->request->get('dv', $this->useDV);
        if( $this->useDV != $this->id ) {
            $this->mode = DetailView::MODE_VIEW;
        }
        
        if( !isset( $this->useId ) ) {
            $this->useId = $this->model->id;
        }
       
        $this->i18n = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/messages',
            'forceTranslation' => true
        ];

        $this->panel['footer'] = 
            '<div class="pull-left">'.
                ($this->canComeBack ? Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Вернуться', Url::toRoute(['index']), ['class' => 'btn btn-primary']).' ' : '').
                ($this->moreButtonsL ? $this->moreButtonsL.' ' : '' ).
            '</div>'.
            '<div class="pull-right">'.
                ($this->mode == DetailView::MODE_VIEW 
                ? 
                    (
                        ($this->moreButtonsR ? $this->moreButtonsR.' ' : '' ).
                        ($this->enableEditMode ? Html::a('<i class="glyphicon glyphicon-pencil"></i> Редактировать', Url::toRoute(['view', 'id' => $this->useId, 'edit' => 't', 'dv' => $this->id]), ['class' => 'btn btn-info']).' ' : '').
                        ($this->canDelete ? Html::a('<i class="glyphicon glyphicon-trash"></i> Удалить', Url::toRoute(['delete', 'id' => $this->useId]), ['class' => 'btn btn-danger', 'data-method' => 'post', 'data-confirm' => $this->askDelete ]) : '')
                    )
                :   
                    (   
                        Html::a('<i class="glyphicon glyphicon-eye-open"></i> Просмотр', Url::toRoute(['view', 'id' => $this->useId]), ['class' => 'btn btn-info']).' '.
                        Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> Сохранить', ['class' => 'btn btn-success', 'data-confirm' => $this->askUpdate])
                    )
                ).
            '</div>'.
            '<div class="clearfix">'.
            '</div>';
       
        parent:: init();
    }

}
