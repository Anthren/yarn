<?php

namespace app\controllers;

use yii\data\Pagination;
use app\models\YarnKind;

class YarnController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $query = YarnKind::find();

        $pagination = new Pagination([
            'defaultPageSize' => 5,
            'totalCount' => $query->count(),
        ]);

        $yarns = $query->orderBy('name')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'yarns' => $yarns,
            'pagination' => $pagination,
        ]);
    }

}
