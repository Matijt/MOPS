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

$this->title = Yii::t('app', 'Recieved seeds');
$this->params['breadcrumbs'][] = "Recieved seeds";
?>
<div class="order-index">

    <h1>Recieved seeds</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    // Seeds on its way
    $seedsOnItsWay = Order::find()->joinWith(['hybridIdHybr'])->where('order.state = "Seeds on its way"')->all();

    $date = date('Y-m-d');
    $proximaSeamana = date('Y-m-d', strtotime("$date + 7 day"));
    $semanaPasada = date('Y-m-d', strtotime("$date - 7 day"));
    foreach($seedsOnItsWay AS $item){
        $fechaEvaluada = date('Y-m-d',  strtotime($item->ssRecDate));

        $primero = ($fechaEvaluada > $proximaSeamana);
        $segundo = ($fechaEvaluada <= $proximaSeamana && $fechaEvaluada > $date);
        $tercero = $fechaEvaluada === $date;
        $cuarto = $fechaEvaluada < $date && $fechaEvaluada >= $semanaPasada;
        $quinto = $fechaEvaluada < $semanaPasada;
        switch ($fechaEvaluada) {
            case $primero:
                $item->action = 'You do not have to worry for this seeds YET.';
                continue;
            case $segundo:
                $item->action = 'Remember you should received '.$item->hybridIdHybr->fatherIdFather->variety.' AND/OR '.$item->hybridIdHybr->motherIdMother->variety.' In the next few days';
                continue;
            case $tercero:
                $item->action = 'You should received '.$item->hybridIdHybr->fatherIdFather->variety.' AND/OR '.$item->hybridIdHybr->motherIdMother->variety.' for the variety '.$item->hybridIdHybr->variety.' TODAY';
                continue;
            case $cuarto:
                $item->action = 'YOU SHOULD HAVE RECEIVED '.$item->hybridIdHybr->fatherIdFather->variety.' AND/OR '.$item->hybridIdHybr->motherIdMother->variety.' for the variety '.$item->hybridIdHybr->variety." already";
                continue;
            case $quinto:
                $item->action = 'YOU REQUIRED TO RECEIVED '.$item->hybridIdHybr->fatherIdFather->variety.' AND/OR '.$item->hybridIdHybr->motherIdMother->variety.' FOR THE VARIETY '.$item->hybridIdHybr->variety.', IT SHOULD HAVE BEN ON THE DAY '.$fechaEvaluada.". IT IS LATE";
                continue;
        }

        $item->save();

    };
    ?>    <?php
    $gridColumns = [
        [       'attribute' => 'Crop',
            'value' => 'numCrop'],
        [       'attribute' => 'Father',
            'value' => 'hybridIdHybr.fatherIdFather.variety'],
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
            if(stristr($model->action , 'YET.') !== FALSE)
            {
                return ['class' => 'success', 'style' => 'color: green;'];
            }
            elseif(stristr($model->action , 'Remember you should') !== FALSE){
                return ['class' => 'info', 'style' => 'color: blue;'];
            }
            elseif(stristr($model->action , 'TODAY') !== FALSE){
                return ['style' => 'background-color: #1fc202; color: #0b5200;'];
            }
            else{
                return ['class' => 'danger', 'style' => 'color: red;'];
            }
        },
        'columns' => [

//            'idorder',
            'numCrop',
            [

                'attribute'=> "prueba",
                'value'=>'hybridIdHybr.fatherIdFather.variety',
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
                'template' => '{view}{update}{delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=order%2Fview&id='.$model->idorder), 'class' => 'modalButtonView'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to('index.php?r=order%2Fupdate&id='.$model->idorder.'&name=arrive'), 'class' => 'modalButtonEdit'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },

                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'index.php?r=order/delete&id='.$model->idorder.'&name=arrive', [
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
