<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\RemarksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Remarks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remarks-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php
        if (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
            echo Html::button(Yii::t('app', 'Create Remark'), ['value' => 'index.php?r=remarks/create', 'class' => 'modalButtonCreate']);
        } ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'remark:ntext',

            ['class' => 'yii\grid\ActionColumn',
                'template' => array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) ?
                    '{view}{update}':'{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=remarks%2Fview&id='.$model->id), 'class' => 'modalButtonView btn btn-primary'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value'  => Url::to('index.php?r=remarks%2Fupdate&id='.$model->id), 'class' => 'modalButtonEdit btn btn-primary'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
