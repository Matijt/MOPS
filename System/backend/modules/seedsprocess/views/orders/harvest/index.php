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
$this->params['breadcrumbs'][] = "Plants to harvest";
?>
<div class="order-index">

    <h1>Plants to harvest</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php
// Seeds arrive
$seedsArrive = Order::find()->joinWith(['hybridIdHybr'])->where('order.state = "Plants after pollen collect and pollination"')->all();

$date = date('Y-m-d');
$proximaSeamana = date('Y-m-d', strtotime("$date + 7 day"));
$semanaPasada = date('Y-m-d', strtotime("$date - 7 day"));
foreach($seedsArrive AS $item){
    $fechaEvaluada = date('Y-m-d',  strtotime($item->harvestF));
    $fechaEvaluadaU = date('Y-m-d',  strtotime($item->harvestU));

    $primero = ($fechaEvaluada > $proximaSeamana);
    $segundo = ($fechaEvaluada <= $proximaSeamana && $fechaEvaluada > $date);
    $tercero = ($date >= $fechaEvaluada && $date <= $fechaEvaluadaU);
    $cuarto =  $date > $fechaEvaluadaU;

    switch ($fechaEvaluada) {
        case $primero:
            $item->action = 'You do not have to worry for this order YET.';
            continue;
        case $segundo:
            $item->action = 'You will be harvesting '.$item->hybridIdHybr->motherIdMother->variety." the next week at the compartment ".$item->compartmentIdCompartment->compNum.".";
            continue;
        case $tercero:
            $item->action = 'The variety '.$item->hybridIdHybr->motherIdMother->variety." is in harvest time from ".$fechaEvaluada." to ".$fechaEvaluadaU." in the compartment: ".$item->compartmentIdCompartment->compNum.".";
            continue;
        case $cuarto:
            $item->action = 'The variety '.$item->hybridIdHybr->motherIdMother->variety." finished the harvest process at ".$fechaEvaluadaU.". You should change the state.";
            continue;
    }

    $item->save();
};

    Pjax::begin(); ?>    <?php
    $gridColumns = [
        [       'attribute' => 'Crop',
            'value' => 'numCrop'],
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
                                            if($model->action == "You do not have to worry for this order YET.")
                                            {
                                                return ['class' => 'success', 'style' => 'color: green;'];
                                            }
                                            elseif(stristr($model->action , 'FINISH') !== FALSE){
                                                return ['class' => 'danger', 'style' => 'color: red;'];
                                            }
                                            else{
                                                return ['class' => 'info', 'style' => 'color: blue;'];
                                            }
                                        },
        'columns' => [

//            'idorder',
            'numCrop',
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
                'template' => '{view}{update}{delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=order%2Fview&id='.$model->idorder), 'class' => 'modalButtonView'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to('index.php?r=order%2Fupdate&id='.$model->idorder), 'class' => 'modalButtonEdit'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },

                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'index.php?r=order/delete&id='.$model->idorder, [
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
<?php Pjax::end(); ?></div>
