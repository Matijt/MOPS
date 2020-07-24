<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\export\ExportMenu;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $model backend\models\NumcropHasCompartment */
/* @var $modelor backend\models\Order */

$this->title = "crop #: ".$model->numcrop_cropnum." compartment: ".$model->compartmentIdCompartment->compNum." crop: ".$model->cropIdcrops->crop;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Numcrop Has Compartments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="numcrop-has-compartment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'numcrop_cropnum',
            [
                'attribute' => 'compartment_idCompartment',
                'value' => $model->compartmentIdCompartment->compNum,
            ],
            ['attribute' => 'createDate',
                'value' => function($data){
                    return date('d-m-Y', strtotime($data->createDate));
                },
            ],
            ['attribute' => 'freeDate',
                'value' => function($data){
                    return date('d-m-Y', strtotime($data->freeDate));
                },
            ],
//            'lastUpdatedDate',
            'rowsOccupied',
            'rowsLeft',
            'cropIdcrops.crop',
        ],
    ]) ?>

    <h1>Orders</h1>

    <?php
    $contador = 0;

    /*foreach ($modelO AS $models){

        $models->ReqDeliveryDate = date('d-m-Y', strtotime($models->ReqDeliveryDate));
        $models->orderDate = date('d-m-Y', strtotime($models->orderDate));
        $models->ssRecDate = date('d-m-Y', strtotime($models->ssRecDate));
        $contador++;
         echo "<h2>Order number: ".$contador."</h2>";

        $atrone = [
            'numCrop',
            'orderKg',
            [
                'attribute' => 'compartment_idCompartment',
                'value' => $models->compartmentIdCompartment->compNum,
            ],
            ['attribute' => 'Hybrid_idHybrid',
                'value' => $models->hybridIdHybr->variety,
            ],
            'calculatedYield',
        ];

        if ($models->numRowsOpt != null) {
            $atrone = array_merge($atrone,
                [
                    'numRowsOpt',
                ]);
        }else{
            $atrone = array_merge($atrone,
                [
                    'numRows',
                ]);
        }

        $atrone = array_merge($atrone,
            [
                'contractNumber',
                'ReqDeliveryDate',
                'sowingM',
                'sowingF',
                'pollinationF',
                'pollinationU',
                'harvestF',
                'harvestU',
            ]);

         echo DetailView::widget([
            'model' => $models,
            'attributes' => $atrone,
        ]);
    }

*/
        $columns = [
            'numCrop',
            'orderKg',
            [
                'attribute'=>'compartment_idCompartment',
                'value'=>'compartmentIdCompartment.compNum',

            ],
            ['attribute' => 'Hybrid_idHybrid',
                'value' => 'hybridIdHybr.variety',
            ],
            'calculatedYield',
            'numRows',
            'numRowsOpt',


            'numRowsOpt' ? 'numRowsOpt':'numRows',

            ['attribute' => 'pollinationF',
                'value' => function($data){
                    return date('d-m-Y', strtotime($data->pollinationF));
                },
            ],
            ['attribute' => 'pollinationU',
                'value' => function($data){
                    return date('d-m-Y', strtotime($data->pollinationU));
                },
            ],
            ['attribute' => 'harvestF',
                'value' => function($data){
                    return date('d-m-Y', strtotime($data->harvestF));
                },
            ],
            ['attribute' => 'harvestU',
                'value' => function($data){
                    return date('d-m-Y', strtotime($data->harvestF));
                },
            ],
        ];



    echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
//            'numCrop',
            [
                'attribute'=>'orderKg',
                'value'=>function($model){
                    return Facil::limitarDecimales($model->orderKg);
                },

            ],
  /*          [
                'attribute'=>'compartment_idCompartment',
                'value'=>'compartmentIdCompartment.compNum',

            ],
    */        ['attribute' => 'Hybrid_idHybrid',
                'value' => 'hybridIdHybr.variety',
            ],
/*            'calculatedYield',
            'numRows',
            'numRowsOpt',*/


              'numRowsOpt' ? 'numRowsOpt':'numRows',

            ['attribute' => 'pollinationF',
                'value' => function($data){
                    return date('d-m-Y', strtotime($data->pollinationF));
                },
            ],
            ['attribute' => 'pollinationU',
                'value' => function($data){
                    return date('d-m-Y', strtotime($data->pollinationU));
                },
            ],
            ['attribute' => 'harvestF',
                'value' => function($data){
                    return date('d-m-Y', strtotime($data->harvestF));
                },
            ],
            ['attribute' => 'harvestU',
                'value' => function($data){
                    return date('d-m-Y', strtotime($data->harvestF));
                },
            ],

        ],
    ]);
    foreach ($modelT as $trial) {
        $contador++;
        echo "<h2>Trials</h2>";

        echo DetailView::widget([
            'model' => $trial,
            'attributes' => [
                'reason',
                'numRows',
                'numCrop',
                'description:ntext',
                'observations:ntext',
                [
                    'attribute' => 'compartment_idCompartment',
                    'value' => $model->compartmentIdCompartment->compNum,
                ],
            ],
        ]);
    }
    ?>
</div>
