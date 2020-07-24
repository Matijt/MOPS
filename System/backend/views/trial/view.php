<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Trial */

$this->title = $model->reason;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Trials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if(isset($_GET['name'])){
    $name = $_GET['name'];
}
?>
<div class="trial-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
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
    ]) ?>


    <?php
    $contador = 0;
    foreach ($modelO AS $models) {
        $contador++;
        echo "<h2>Order number: " . $contador . "</h2>";

       $atrone = [
            ['attribute' => 'Hybrid_idHybrid',
                'value' => $models->hybridIdHybr->variety,
            ],
        ];

        $atrone = array_merge($atrone,
            [
                'ReqDeliveryDate',
                'orderDate',
                'ssRecDate',
            ]);

        $one = DetailView::widget([
            'model' => $models,
            'attributes' => $atrone,
        ]);


        $atrtwo = [
            ['attribute' => 'Hybrid_idHybrid',
                'value' => $models->hybridIdHybr->variety,
            ],
        ];
                $atrtwo = array_merge($atrtwo,
                    [
                        'netNumOfPlantsF',
                        'netNumOfPlantsM',
                        'sowingF',
                        'sowingM',
                        'nurseryF',
                        'nurseryM',
                    ]);

        $two = DetailView::widget([
            'model' => $models,
            'attributes' => $atrtwo,
        ]);


        $atr2 = [
            ['attribute' => 'Hybrid_idHybrid',
                'value' => $models->hybridIdHybr->variety,
            ]
        ];

                $atr2 = array_merge($atr2,
                    [
                        'realisedNrOfPlantsM',
                        'extractedPlantsM',
                        'remainingPlantsM',
                        'realisedNrOfPlantsF',
                        'extractedPlantsF',
                        'remainingPlantsF',
                    ]);


        $two2 = DetailView::widget([
            'model' => $models,
            'attributes' => $atr2,
        ]);

        $atr = [
            ['attribute' => 'Hybrid_idHybrid',
                'value' => $models->hybridIdHybr->variety,
            ]
        ];
                $atr = array_merge($atr,
                    [
                        'sowingDateM',
                        'sowingDateF',
                        'transplantingM',
                        'transplantingF',
                        'pollenColectF',
                        'pollenColectU',
                        'pollinationF',
                        'pollinationU',
                        'harvestF',
                        'harvestU',
                        'steamDesinfectionF',
                        'steamDesinfectionU',
                    ]);

        $three = DetailView::widget([
            'model' => $models,
            'attributes' => $atr,
        ]);
        $four = DetailView::widget([
            'model' => $models,
            'attributes' => [
                'remarks:ntext'

            ],
        ]);
        
    echo \kartik\tabs\TabsX::widget([
        'items' => [
            [
                'label' => 'Basic info '.$contador,
                'content' => $one,
                'active' => true,
            ],
            [
                'label' => 'Sowing',
                'items' => [
                    [
                        'label' => 'Info',
                        'content' => $two,
                    ],
                    [
                        'label' => 'Extra',
                        'content' => $two2,
                    ],
                ],
            ],
            [
                'label' => 'Date info',
                'content' => $three,
            ],
        ],
    ]);
    echo $four;

    }
    ?>

</div>
