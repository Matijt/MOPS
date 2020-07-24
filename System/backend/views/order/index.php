<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use backend\codigo\Facil;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php

        if (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
            echo Html::button(Yii::t('app', 'Create Order'), ['value' => 'index.php?r=order/create', 'class' => 'modalButtonCreate']);
        }?>
    </p>

 <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'emptyText' => 'If you do not find what you are looking for, remember to look at Orders->View Pollen Production.',
        'rowOptions' => function($model){
                                            if($model->check == "Great, no problem."){
                                                return ['class' => 'success', 'style' => 'color: green;'];
                                            }else{
                                                return ['class' => 'danger', 'style' => 'color: red;'];
                                            }
                                        },
        'columns' => [
            'numCrop',
            [
                'attribute'=>'Hybrid_idHybrid',
                'value'=>'hybridIdHybr.variety',
            ],
            [
                'attribute'=>'compartment_idCompartment',
                'value'=>'compartmentIdCompartment.compNum',
            ],
            [
                'attribute' => 'orderKg',
                'value' => function($model){
                    return Facil::limitarDecimales($model->orderKg);
                }
            ],
            'numRowsOpt',
            [
                'attribute' => 'ReqDeliveryDate',
                'value' => 'ReqDeliveryDate',
                'format' => ['date', 'php:d/m/Y'],
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'ReqDeliveryDate',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy'
                    ]
                ])
            ],
            [
                'attribute' => 'steamDesinfectionU',
                'value' => 'steamDesinfectionU',
                'format' => ['date', 'php:d/m/Y'],
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'steamDesinfectionU',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy'
                    ]
                ])
            ],
            // 'orderDate',
            // 'contractNumber',
            // 'calculatedYield',
            // 'ssRecDate',
            // 'sowingM',
            // 'sowingF',
            // 'nurseryM',
            // 'nurseryF',
            // 'check',
            // 'sowingDateM',
            // 'sowingDateF',
            // 'realisedNrOfPlantsM',
            // 'realisedNrOfPlantsF',
            // 'transplantingM',
            // 'transplantingF',
            // 'extractedPlantsF',
            // 'extractedPlantsM',
            // 'remainingPlantsF',
            // 'remainingPlantsM',
            // 'pollinationF',
            // 'pollinationU',
            // 'harvestF',
            // 'harvestU',
            // 'steamDesinfectionF',
            // 'steamDesinfectionU',
            // 'remarks:ntext',
            // 'compartment_idCompartment',
            // 'plantingDistance',
            // 'Hybrid_idHybrid',

            ['class' => 'yii\grid\ActionColumn',
                'template' => (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) ?
                    '{view}{update}{delete}':'{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=order%2Fview&id='.$model->idorder), 'class' => 'modalButtonView'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        if (!(strpos($actual_link, 'page') !== false)){
                            $edit = 'index.php?r=order%2Fupdate&id='.$model->idorder;
                        }else{
                            $edit = 'index.php?r=order%2Fupdate&id='.$model->idorder."&page=".$_GET['page'];
                        }
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to($edit), 'class' => 'modalButtonEdit'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },

                    'delete' => function ($url, $model) {

                            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                            if (!(strpos($actual_link, 'page') !== false)) {
                                $edit = 'index.php?r=order/delete&id=' . $model->idorder;
                            } else {
                                $edit = 'index.php?r=order/delete&id=' . $model->idorder . "&page=" . $_GET['page'];
                            }
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $edit, [
                                'data-method' => 'POST',
                                'title' => Yii::t('app', 'Update'),
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
