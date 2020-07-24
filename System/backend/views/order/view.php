<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = $model->ReqDeliveryDate . " " . $model->hybridIdHybr->variety;;
if(isset($_GET['name'])){
    $name = $_GET['name'];
}
$this->title = Yii::t('app', 'View {modelClass}: ', [
        'modelClass' => 'Order',
    ]) . $model->ReqDeliveryDate." ".$model->hybridIdHybr->variety;
if(isset($name)){
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'state'), 'url' => [$name."index"]];
}else {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders'), 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php

    $model->ReqDeliveryDate = date('d-m-Y', strtotime($model->ReqDeliveryDate));
    $model->orderDate = date('d-m-Y', strtotime($model->orderDate));
    $model->ssRecDate = date('d-m-Y', strtotime($model->ssRecDate));
    $model->sowingDateM = date('d-m-Y', strtotime($model->sowingDateM));
    $model->sowingDateF = date('d-m-Y', strtotime($model->sowingDateF));
    $model->transplantingM = date('d-m-Y', strtotime($model->transplantingM));
    $model->transplantingF = date('d-m-Y', strtotime($model->transplantingF));
    $model->pollenColectF = date('d-m-Y', strtotime($model->pollenColectF));
    $model->pollenColectU = date('d-m-Y', strtotime($model->pollenColectU));
    $model->pollinationF = date('d-m-Y', strtotime($model->pollinationF));
    $model->pollinationU = date('d-m-Y', strtotime($model->pollinationU));
    $model->harvestF = date('d-m-Y', strtotime($model->harvestF));
    $model->harvestU = date('d-m-Y', strtotime($model->harvestU));
    $model->steamDesinfectionF = date('d-m-Y', strtotime($model->steamDesinfectionF));
    $model->steamDesinfectionU = date('d-m-Y', strtotime($model->steamDesinfectionU));

    if($model->sowingDateF != null) {
        $atrone = [
            'numCrop',
            [
                'attribute' => 'orderKg',
                'value' => function($model){
                    return Facil::limitarDecimales($model->orderKg);
                }
            ],
            [
                'attribute' => 'gpOrder',
                'value' => function($model){
                    return Facil::limitarDecimales($model->gpOrder);
                }
            ],
            [
                'label' => 'Mother Grams Per Plant',
                'value' => function($model){
                    return Facil::limitarDecimales($model->hybridIdHybr->motherIdMother->gP);
                },
            ],
            [
                'attribute' => 'compartment_idCompartment',
                'value' => $model->compartmentIdCompartment->compNum,
            ],
            ['attribute' => 'Hybrid_idHybrid',
                'value' => $model->hybridIdHybr->variety,
            ],
            'calculatedYield',
            'numRows',
        ];


        if ($model->numRowsOpt != null) {
            $atrone = array_merge($atrone,
                [
                    'numRowsOpt',
                ]);
        }

        $atrone = array_merge($atrone,
            [
                [
                    'attribute' => 'FMRatio',
                    'value' => function($model){
                        return Facil::limitarDecimales($model->FMRatio);
                    },
                ],
                [
                    'attribute' => 'Density',
                    'value' => function($model){
                        return Facil::limitarDecimales($model->Density);
                    },
                ],
                'NumOfPlantsPerRow',
                'NumOfFPRow',
                'NumOfMPRow',
                'contractNumber',
                'ReqDeliveryDate',
                'orderDate',
                'ssRecDate',
            ]);

        $one = DetailView::widget([
            'model' => $model,
            'attributes' => $atrone,
        ]);


        $atrtwo = [
            'numCrop',
            [
                'attribute' => 'orderKg',
                'value' => function($model){
                    return Facil::limitarDecimales($model->orderKg);
                }
            ],
            [
                'attribute' => 'gpOrder',
                'value' => function($model){
                    return Facil::limitarDecimales($model->gpOrder);
                }
            ],
            [
                'attribute' => 'compartment_idCompartment',
                'value' => $model->compartmentIdCompartment->compNum,
            ],
            ['attribute' => 'Hybrid_idHybrid',
                'value' => $model->hybridIdHybr->variety,
            ],
        ];


        if ($model->sowingDateF == '1970-01-01') {
            $atrtwo = array_merge($atrtwo,
                [
                    'netNumOfPlantsM',
  //                  'sowingM',
                    'nurseryM',
                ]);
        } else
            if ($model->prueba) {
                $atrtwo = array_merge($atrtwo,
                    [
                        'netNumOfPlantsF',
//                        'sowingF',
                        'nurseryF',
                    ]);
            } else {
                $atrtwo = array_merge($atrtwo,
                    [
                        'netNumOfPlantsF',
                        'netNumOfPlantsM',
    //                    'sowingF',
      //                  'sowingM',
                        'nurseryF',
                        'nurseryM',
                    ]);
            }
        $two = DetailView::widget([
            'model' => $model,
            'attributes' => $atrtwo,
        ]);


        $atr2 = [
            'numCrop',
            [
                'attribute' => 'orderKg',
                'value' => function($model){
                    return Facil::limitarDecimales($model->orderKg);
                }
            ],
            [
                'attribute' => 'gpOrder',
                'value' => function($model){
                    return Facil::limitarDecimales($model->gpOrder);
                }
            ],
            [
                'attribute' => 'compartment_idCompartment',
                'value' => $model->compartmentIdCompartment->compNum,
            ],
            ['attribute' => 'Hybrid_idHybrid',
                'value' => $model->hybridIdHybr->variety,
            ]
        ];

        if ($model->sowingDateF == '1970-01-01') {
            $atr2 = array_merge($atr2,
                [
                    'realisedNrOfPlantsM',
                    'extractedPlantsM',
                    'remainingPlantsM',
                ]);
        } else
            if ($model->prueba) {
                $atr2 = array_merge($atr2,
                    [
                        'realisedNrOfPlantsF',
                        'extractedPlantsF',
                        'remainingPlantsF',
                    ]);
            } else {
                $atr2 = array_merge($atr2,
                    [
                        'realisedNrOfPlantsM',
                        'extractedPlantsM',
                        'remainingPlantsM',
                        'realisedNrOfPlantsF',
                        'extractedPlantsF',
                        'remainingPlantsF',
                    ]);
            }


        $two2 = DetailView::widget([
            'model' => $model,
            'attributes' => $atr2,
        ]);

        $atr = [
            'numCrop',
            [
                'attribute' => 'orderKg',
                'value' => function($model){
                    return Facil::limitarDecimales($model->orderKg);
                }
            ],
            [
                'attribute' => 'gpOrder',
                'value' => function($model){
                    return Facil::limitarDecimales($model->gpOrder);
                }
            ],
            [
                'attribute' => 'compartment_idCompartment',
                'value' => $model->compartmentIdCompartment->compNum,
            ],
            ['attribute' => 'Hybrid_idHybrid',
                'value' => $model->hybridIdHybr->variety,
            ]
        ];
        if ($model->sowingDateF == '1970-01-01') {
            $atr = array_merge($atr,
                [
                    'sowingDateM',
                    'transplantingM',
                    'pollenColectF',
                    'pollenColectU',
                    'steamDesinfectionF',
                    'steamDesinfectionU',
                ]);
        } else
            if ($model->prueba) {
                $atr = array_merge($atr,
                    [
                        'sowingDateF',
                        'transplantingF',
                        'pollinationF',
                        'pollinationU',
                        'harvestF',
                        'harvestU',
                        'steamDesinfectionF',
                        'steamDesinfectionU',
                    ]);
            } else {
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
            }


        $three = DetailView::widget([
            'model' => $model,
            'attributes' => $atr,
        ]);

        if ($model->rfselectorc == null || $model->rfselectorc == '') {
            $four = DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute'=>'remarks',
                        'value' => \backend\models\Remarks::findOne($model->remarks)->remark,
                    ],

                ],
            ]);
        }else{
            $four = DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute'=>'remarks',
                        'value' => \backend\models\Remarks::findOne($model->remarks)->remark,
                    ],
                    'rfselectorc:ntext'
                ],
            ]);
        }
    }
    else{
        // Producción de Pollen
        $atrone = [
            'numCrop',
            [
                'attribute' => 'compartment_idCompartment',
                'value' => $model->compartmentIdCompartment->compNum,
            ],
            [
                'label' => 'Father',
                'attribute' => 'Father_idFather',
                'value' => $model->hybridIdHybr->fatherIdFather->variety,
            ],
            'numRows',
        ];

        $atrone = array_merge($atrone,
            [
                'contractNumber',
                'ReqDeliveryDate',
                'orderDate',
                'ssRecDate',
            ]);

        $one = DetailView::widget([
            'model' => $model,
            'attributes' => $atrone,
        ]);


        $atrtwo = [
            'numCrop',
            [
                'attribute' => 'compartment_idCompartment',
                'value' => $model->compartmentIdCompartment->compNum,
            ],
            [
                'label' => 'Father',
                'attribute' => 'Father_idFather',
                'value' => $model->hybridIdHybr->fatherIdFather->variety,
            ],
            'numRows',
        ];


            $atrtwo = array_merge($atrtwo,
                [
                    'netNumOfPlantsM',
                    'sowingM',
                    'nurseryM',
                ]);

        $two = DetailView::widget([
            'model' => $model,
            'attributes' => $atrtwo,
        ]);


        $atr2 = [
            'numCrop',
            [
                'attribute' => 'compartment_idCompartment',
                'value' => $model->compartmentIdCompartment->compNum,
            ],
            [
                'label' => 'Father',
                'attribute' => 'Father_idFather',
                'value' => $model->hybridIdHybr->fatherIdFather->variety,
            ],
            'numRows',
        ];

            $atr2 = array_merge($atr2,
                [
                    'realisedNrOfPlantsM',
                    'extractedPlantsM',
                    'remainingPlantsM',
                ]);


        $two2 = DetailView::widget([
            'model' => $model,
            'attributes' => $atr2,
        ]);

        $atr = [
            'numCrop',
            [
                'attribute' => 'compartment_idCompartment',
                'value' => $model->compartmentIdCompartment->compNum,
            ],
            [
                'label' => 'Father',
                'attribute' => 'Father_idFather',
                'value' => $model->hybridIdHybr->fatherIdFather->variety,
            ],
            'numRows',
        ];
            $atr = array_merge($atr,
                [
                    'sowingDateM',
                    'transplantingM',
                    'pollenColectF',
                    'pollenColectU',
                    'steamDesinfectionF',
                    'steamDesinfectionU',
                ]);


        $three = DetailView::widget([
            'model' => $model,
            'attributes' => $atr,
        ]);
        if ($model->rfselectorc == null || $model->rfselectorc == '') {
            $four = DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute'=>'remarks',
                        'value' => \backend\models\Remarks::findOne($model->remarks)->remark,
                    ],
                ],
            ]);
        }else{
            $four = DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute'=>'remarks',
                        'value' => \backend\models\Remarks::findOne($model->remarks)->remark,
                    ],
                    'rfselectorc:ntext'
                ],
            ]);
        }
    }
    ?>

<?= \kartik\tabs\TabsX::widget([
    'items' => [
        [
        'label' => 'Basic info',
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
?>
    <?=
    $four;
    ?>

</div>
