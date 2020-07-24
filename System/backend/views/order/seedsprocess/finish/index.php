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
use backend\models\Order;
use kartik\export\ExportMenu;
use yii\helpers\Url;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = "Orders finish";
?>
<div class="order-index">

    <h1>Orders finished</h1>

<?php
// Seeds arrive
$seedsArrive = Order::find()->joinWith(['hybridIdHybr'])
    ->andFilterWhere(['=', 'order.delete',0])
    ->andFilterWhere(['<','steamDesinfectionU','date(\'Y-m-d\')'])->all();

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
        'orderKg',
    ];
?>
    <p>
        <?php

        if (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
            echo Html::button(Yii::t('app', 'Create Order'), ['value' => 'index.php?r=order/createf', 'class' => 'modalButtonCreate']);
        }?>
    </p>

<?php
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
    ]);
    if(isset($primero) && $primero == 0){
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
                                            if($model->selector == "Inactive") {
                                                    $color = ['style' => 'color: red; background: #f2dede;'];
                                                }else{
                                                    $color = ['class' => 'success', 'style' => 'color: green;'];
                                                };

                                            return $color;
                                        },
        'columns' => [
            //  'idorder',
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
/*            ['attribute'=>'selector',
                'value' => 'selector',
            ]
            ,*/
            // 'gpOrder',
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
                    '{view}{edit}{editf}{inactive}':'{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=order%2Fview&id='.$model->idorder), 'class' => 'modalButtonView'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },
                    'edit' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to('index.php?r=order%2Fupdate&id='.$model->idorder), 'class' => 'modalButtonEdit'], [
                            'title' => Yii::t('app', 'Edit'),
                        ]);
                    },
                    'editf' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to('index.php?r=order%2Fupdatef&id='.$model->idorder), 'class' => 'modalButtonCreate'], [
                            'title' => Yii::t('app', 'Edit'),
                        ]);
                    },
                    'inactive' => function ($url, $model) {

                        return Html::button('<span class="glyphicon glyphicon-eye-close"></span>', ['value' => Url::to('index.php?r=order%2Fupdates&id='.$model->idorder), 'class' => 'modalButtonDeleteM'], [
                            'title' => Yii::t('app', 'Delete'),
                        ]);
                    },
                ],
            ],

        ],

    ]);
    ?>

</div>
<script>
    $(".modalButtonDeleteM").addClass('btn btn-danger');

    // get the click event of the create button
    $(".modalButtonDeleteM").click(function(){
        $('#edit').modal('show').find('#editContent').load($(this).attr('value'));
//        alert("create");
    });
</script>