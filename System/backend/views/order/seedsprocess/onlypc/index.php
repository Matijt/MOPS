<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\DataColumn;
use yii\grid\CheckboxColumn;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
use backend\models\Order;
use kartik\export\ExportMenu;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = "Pollen collect and pollination";
?>
<div class="order-index">

    <h1>Pollination</h1>

    <?php
    // Seeds arrive
    $seedsArrive = Order::find()->joinWith(['hybridIdHybr'])
        ->andFilterWhere(['=', 'order.delete',0])
        ->andFilterWhere(['<=','order.pollinationF',date('Y-m-d')])
        ->andFilterWhere(['>=','order.pollinationU',date('Y-m-d')])
        ->all();


    $date = date('Y-m-d');
    $proximaSeamana = date('Y-m-d', strtotime("$date + 7 day"));
    $semanaPasada = date('Y-m-d', strtotime("$date - 7 day"));
    foreach($seedsArrive AS $item){

        $fechaEvaluadaPlf = date('Y-m-d',  strtotime($item->pollinationF));
        $fechaEvaluadaPlu = date('Y-m-d',  strtotime($item->pollinationU));

        $fechaLimite = date('Y-m-d',  strtotime($item->pollinationF));

        $primero =($fechaEvaluadaPlf <= $date && $fechaEvaluadaPlu >= $date);

        switch ($date) {
            case $primero:
                $item->action = 'The pollinition of '.$item->hybridIdHybr->motherIdMother->variety." has started on the compartment: ".$item->compartmentIdCompartment->compNum;
                continue;
            default:
                $item->action = 'You should try changing the "state" value to see the previous state of this order.';
                continue;
        }

        $item->save();
    };

    $gridColumns = [
        [       'attribute' => 'Crop',
            'value' => 'numCrop'],
        [       'attribute' => 'Order',
            'value' => 'orderKg'],
        [       'attribute' => 'Mother',
            'value' => 'hybridIdHybr.motherIdMother.variety'],
        [       'attribute' => 'Hybrid',
            'value' => 'hybridIdHybr.variety'],
        [       'attribute' => 'Compartment',
            'value' => 'compartmentIdCompartment.compNum'],
        'action',
    ];

    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
    ]);
    if(isset($primero) && $primero == 0){
        // $dataProvider->query->andWhere(["like", "state", "Seeds arrive"])->orWhere(["like", "state", "Seeds on its way"]);
        $primero = 1;
    }
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model){
            if($model->action == 'You should try changing the "state" value to see the previous state of this order.')
            {
                return ['class' => 'warning', 'style' => 'color: #d2b104;'];
            }
            elseif(stristr($model->action , 'AND POLLINATION') !== FALSE){
                return ['class' => 'info', 'style' => 'color: blue;'];
            }
            elseif(stristr($model->action , 'collect of') !== FALSE){
                return ['class' => 'info', 'style' => 'color: green;'];
            }
            elseif(stristr($model->action , 'FINISHED') !== FALSE){
                return ['class' => 'danger', 'style' => 'color: red;'];
            }
            elseif(stristr($model->action , 'The pollination ') !== FALSE){
                return ['class' => 'success'];
            }
            else{
                return ['class' => 'info', 'style' => 'color: blue;'];
            }
        },
        'columns' => [

//            'idorder',
            'numCrop',
            'orderKg',
            [
                'attribute'=> "prueba2",
                'value'=>'hybridIdHybr.motherIdMother.variety',
            ],
            [
                'attribute'=>'Hybrid_idHybrid',
                'value'=>'hybridIdHybr.variety',
            ],
            [
                'attribute'=>'compartment_idCompartment',
                'value'=>'compartmentIdCompartment.compNum',
            ],
            [
                'attribute'=>'action',
                'value'=>'action',
                'contentOptions'=>[
                    'style'=>
                        ['width: 1px;',],
                ],
                'headerOptions' => ['width' => '70'],
                'contentOptions' => [
                    'style'=>'max-width:500px; overflow: auto; word-wrap: break-word;white-space: nowrap;'
                ],
            ],
            // 'numRows',
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
                'template' => array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) ?
                    '{view}{update}{delete}':'{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=order%2Fview&id='.$model->idorder), 'class' => 'modalButtonView'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to('index.php?r=order%2Fupdate&id='.$model->idorder.'&name=onlypc'), 'class' => 'modalButtonEdit'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },

                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'index.php?r=order/delete&id='.$model->idorder.'&name=onlypc', [
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

    ]);
    ?>
</div>
