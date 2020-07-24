<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\NumcropHasCompartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Surfaces planning');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="numcrop-has-compartment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model){
            if($model->cropIdcrops->crop == "Not planned"){
                return ['class' => 'danger', 'style' => 'color: red;'];
            }else if($model->cropIdcrops->crop == "Canceled"){
                return ['style' => 'color: white; background: black;'];
            };
            if($model->estado == "Activo" || $model->estado == "Active"){
                return ['class' => 'success', 'style' => 'color: green;'];
            }else{
                return ['style' => 'color: white; background: black;'];
            }
        },
        'columns' => [
            'numcrop_cropnum',
            [
                'attribute'=>'compartment_idCompartment',
                'value'=>'compartmentIdCompartment.compNum',
            ],
            [
                'attribute'=>'createDate',
                'value'=>function($model){
                    if ($model->createDate) {
                        return date('d-m-Y', strtotime($model->createDate));
                    }else{
                        return null;
                    }
                },
            ],
            [
                'attribute'=>'freeDate',
                'value'=>function($model){
                    if ($model->freeDate) {
                        return date('d-m-Y', strtotime($model->freeDate));
                    }else{
                        return null;
                    }
                },
            ],
            //'lastUpdatedDate',
             'rowsOccupied',
            'rowsLeft',
//            'rowsLeftOpt',
            [
                'attribute'=>'crop_idcrops',
                'value'=>'cropIdcrops.crop',
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Holland', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) ?
                    '{view}{update}':'{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=numcrop-has-compartment%2Fview&numcrop_cropnum='.$model->numcrop_cropnum."&compartment_idCompartment=".$model->compartment_idCompartment), 'class' => 'modalButtonView'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        if (!(strpos($actual_link, 'page') !== false)){
                            $edit = 'index.php?r=numcrop-has-compartment%2Fupdate&numcrop_cropnum='.$model->numcrop_cropnum."&compartment_idCompartment=".$model->compartment_idCompartment;
                        }else{
                            $edit = 'index.php?r=numcrop-has-compartment%2Fupdate&numcrop_cropnum='.$model->numcrop_cropnum."&compartment_idCompartment=".$model->compartment_idCompartment."&page=".$_GET['page'];
                        }
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to($edit), 'class' => 'modalButtonEdit'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
</div>
