<?php

namespace app\components\widgets;

use yii\widgets\LinkPager as BaseLinkPager;

class LinkPager extends BaseLinkPager
{
    public $maxButtonCount = 5;
    public $prevPageLabel = '&laquo;';
    public $nextPageLabel = '&raquo;';
    public $firstPageLabel = '&laquo;&laquo;';
    public $lastPageLabel = '&raquo;&raquo;';
    
    public function init()
    {
        $pageCount = $this->pagination->getPageCount();
        $currentPage = $this->pagination->getPage();
        
        if( $currentPage == 0 ) {
            //$this->firstPageLabel = false;
            //$this->prevPageLabel = false;
        }
        if( $currentPage == $pageCount - 1 ) {
            //$this->lastPageLabel = false;
            //$this->nextPageLabel = false;
        }
        if( $currentPage <= floor( $this->maxButtonCount / 2 ) ) {
            //$this->firstPageLabel = false;
        }
        if( $currentPage >= $pageCount - ceil( $this->maxButtonCount / 2 ) ) {
            //$this->lastPageLabel = false;
        }
    }
}