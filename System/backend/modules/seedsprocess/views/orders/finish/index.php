<script type="text/javascript">
    $(document).ready(function () {
        $(':checkbox').change(function () {
                $.post("index.php?r=order/change&id="+($(this).val()), function( data ){
//                    alert(data);
                    });
        });
    });
</script>

<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use backend\models\Order;
use kartik\export\ExportMenu;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = "Orders finish";
?>
<div class="order-index">

    <h1>Orders finish</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php
// Seeds arrive
$seedsArrive = Order::find()->joinWith(['hybridIdHybr'])->where('order.state = "Order finish"')->all();

$date = date('Y-m-d');
$proximaSeamana = date('Y-m-d', strtotime("$date + 7 day"));
$semanaPasada = date('Y-m-d', strtotime("$date - 7 day"));
foreach($seedsArrive AS $item){
    $fechaEvaluada = date('Y-m-d',  strtotime($item->steamDesinfectionU));

    $primero = ($fechaEvaluada > $date);

    if($primero){
        $item->action = 'This order haven´t finished yet';
    }else{
        $item->action = 'This order has already finished';
    }
    $item->save();
};
    Pjax::begin(); ?>    <?php
    $gridColumns = [
        [       'attribute' => 'Crop',
            'value' => 'numCrop'],
        [       'attribute' => 'Mother',
            'value' => 'hybridIdHybr.motherIdMother.variety'],
        ['attribute'=> "prueba",
            'value'=>'hybridIdHybr.fatherIdFather.variety',
        ],
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
                                            $color = "";
                                            if((stristr($model->action , 'yet') !== FALSE)){
                                                $color = ['style' => 'color: red; background: #f2dede;'];
                                            }else{
                                                $color = ['class' => 'success', 'style' => 'color: green;'];
                                            };
                                            if($model->selector = "Inactive") {
                                                if((stristr($model->action , 'yet') !== FALSE)){
                                                    $color = ['style' => 'color: red; background: #f2dede;', 'checked' => 'true'];
                                                }else{
                                                    $color = ['class' => 'success', 'style' => 'color: green;', 'checked' => 'true'];
                                                };
                                            }
                                            return $color;
                                        },
        'pjax' => true,

        'pjaxSettings'=>[
            'neverTimeout'=>true,
        ],
        'columns' => [
                [
                    'rowSelectedClass' => 'warning',
                    'attribute' => 'idorder',
                    'header' => 'select',
                    'class' => '\kartik\grid\CheckboxColumn',
                    'contentOptions'=>[
                        'style'=>
                            ['background: blue;',],
                    ],

                ],
            //  'idorder',
            'numCrop',
            [
                'attribute'=> "prueba2",
                'value'=>'hybridIdHybr.motherIdMother.variety',
            ],
            'gpOrder',
            [
                'attribute'=>'Hybrid_idHybrid',
                'value'=>'hybridIdHybr.variety',
            ],
            [
                'attribute'=>'compartment_idCompartment',
                'value'=>'compartmentIdCompartment.compNum',
            ],
            ['attribute'=>'selector',
                'value' => 'selector',
            ]
            ,
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

<?php Pjax::end(); ?>
</div>
