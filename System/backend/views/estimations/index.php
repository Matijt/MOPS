<?php

use yii\helpers\Html;
use yii\grid\GridView;use yii\helpers\Url;
use backend\codigo\Facil;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\EstimationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Estimations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="estimations-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="row">
        <div class="col-lg-4">
            <p>
                <?php
                if (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
                    echo Html::button(Yii::t('app', 'Create Estimation'), ['value' => 'index.php?r=estimations/create', 'class' => 'modalButtonCreate']);
                }
                ?>
            </p>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model){
        if ($model->difference >= 0) {
            return ['class' => 'success', 'style' => 'color: green;'];
        }else{
            return ['class' => 'danger', 'style' => 'color: red;'];
        }
        },
        'columns' => [
            [
                'value'=>'orderIdorder.numCrop',
                'label' => 'Crop',
            ],
            [
                'attribute'=>'order_idorder',
                'value'=>'orderIdorder.compartmentIdCompartment.compNum',
                'label' => 'Compartment',
            ],
            [
                'attribute'=>'variety',
                'value'=>'orderIdorder.hybridIdHybr.variety',
            ],
            [
                'attribute'=>'totalHarvest',
                'value' => function($model){
                    return Facil::limitarDecimales($model->totalHarvest);
                },
            ],
            [
                'attribute'=>'orderKg',
                'value' => function($model){
                    return Facil::limitarDecimales($model->orderIdorder->orderKg);
                },
            ],
            [
                'attribute'=>'difference',
                'value'=>function($model){
        return $model->difference."%";
                },
            ],
            'LUser',

            ['class' => 'yii\grid\ActionColumn',
                'template' => (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) ?
                    '{view}{update}{delete}':'{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=estimations%2Fview&id='.$model->idestimations), 'class' => 'modalButtonView'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to('index.php?r=estimations%2Fupdate&id='.$model->idestimations), 'class' => 'modalButtonEdit'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },

                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'index.php?r=estimations/delete&id='.$model->idestimations, [
                            'data-method' => 'POST',
                            'title' => Yii::t('app', 'Update'),
                            'data-confirm' => "¿Está seguro que desea eliminar esta categoría?",
                            'role' => 'button',
                            'class' => 'modalButtonDelete',
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
</div>
