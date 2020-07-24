<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\RegistrynurserySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Registry of Nurseries';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="registrynursery-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php
        if (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
            echo Html::button(Yii::t('app', 'Create Registry For Nursery'), ['value' => Url::to('index.php?r=registrynursery/create'), 'class' => 'modalButtonCreate']);
        }
        ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'compNum',
                'value' => 'orderIdorder.compartmentIdCompartment.compNum',
            ],
            [
                'attribute' => 'hybrid',
                'value' => 'orderIdorder.hybridIdHybr.variety',
            ],
            [
                'attribute' => 'seedsUsed',
                'value'=>function($model){
                    return Facil::limitarDecimales($model->seedsUsed);
                },
            ],
            [
                'attribute' => 'sowedTable',
                'value'=>function($model){
                    return Facil::limitarDecimales($model->sowedTable);
                },
            ],
            [
                'attribute' => 'TidelFloor',
                'value'=>function($model){
                    return Facil::limitarDecimales($model->TidelFloor);
                },
            ],
//            'seedsUsed',
            //'FM',
//            'sowedTable',
  //          'TidelFloor',
            //'germination',
            //'sowedSeeds',
            //'recievedSeeds',
            //'germinatedSeeds',
            //'remarks:ntext',
            //'nursery_idnursery',

            ['class' => 'yii\grid\ActionColumn',
                'template' => (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) ?
                    '{view}{update}{delete}':'{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=registrynursery%2Fview&id='.$model->id), 'class' => 'modalButtonView'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to('index.php?r=registrynursery%2Fupdate&id='.$model->id), 'class' => 'modalButtonEdit'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },

                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'index.php?r=registrynursery/delete&id='.$model->id, [
                            'data-method' => 'POST',
                            'title' => Yii::t('app', 'Delete'),
                            'data-confirm' => "Are you sure to delete this item?",
                            'role' => 'button',
                            'class' => 'modalButtonDelete',
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
</div>
