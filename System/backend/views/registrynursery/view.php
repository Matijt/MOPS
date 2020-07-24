<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $model backend\models\Registrynursery */

$this->title = "Registry ".$model->orderIdorder->hybridIdHybr->variety;
$this->params['breadcrumbs'][] = ['label' => 'Registry of Nurseries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$model->recDate = date('d-m-Y', strtotime($model->recDate));
$model->sowing = date('d-m-Y', strtotime($model->sowing));
$model->transplant = date('d-m-Y', strtotime($model->transplant));
$model->trasplantCompartment = date('d-m-Y', strtotime($model->trasplantCompartment));
?>
<div class="registrynursery-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    $atrg = [
        [
            'label' => 'Compartment',
            'value' => $model->orderIdorder->compartmentIdCompartment->compNum,
        ],
        [
            'attribute' => 'FM',
            'value' => function($model){
                if ($model->FM == 'F'){
                    return 'Female';
                }else{
                    return 'Male';
                }
            }
        ],
        [
            'label' => 'Parent',
            'value' => function($model){
                if ($model->FM == 'F'){
                    return $model->orderIdorder->hybridIdHybr->motherIdMother->variety;
                }else{
                    return $model->orderIdorder->hybridIdHybr->fatherIdFather->variety;
                }
            }
        ],
        [
            'label' => 'Variety',
            'value' => function($model){
                return $model->orderIdorder->hybridIdHybr->variety;
            }
        ],
    ];

    $atrone = array_merge($atrg,
        [
            'batch',
            'prodLot',
            'shipment'
        ]);

    $one = DetailView::widget([
        'model' => $model,
        'attributes' => $atrone,
    ]);


    $atrtwo = array_merge($atrg,
        [
            'recDate',
            'seedsRecieved',
            'realSeedsRecieved',
            'seedsUsed',
            'remain'
        ]);

    $two = DetailView::widget([
        'model' => $model,
        'attributes' => $atrtwo,
    ]);


    $atrtwo2 = array_merge($atrg,
        [
            [
                'label' => 'Rows',
                'value' => function($model){
                    if ($model->numRows == 0 || $model->numRows == null){
                        if ($model->orderIdorder->numRowsOpt == null || $model->orderIdorder->numRowsOpt == null){
                            return $model->orderIdorder->numRows;
                        }else{
                            return $model->orderIdorder->numRowsOpt;
                        }
                    }else{
                        return $model->numRows;
                    }
                }
            ],
            [
                'label' => 'Num Plants',
                'value' => function($model){
                    if ($model->numPlants == 0 || $model->numPlants == null){
                        return $model->orderIdorder->NumOfPlantsPerRow;
                    }else{
                        return $model->numPlants;
                    }
                }
            ],
            'estimatedGermination',
            [
                'attribute'=>'usedGermination',
                'value'=>function($model){
                    return Facil::limitarDecimales($model->usedGermination);
                },
            ],
            'plantsPerCompartment',
        ]);

    $two2 = DetailView::widget([
        'model' => $model,
        'attributes' => $atrtwo2,
    ]);


    $atrthree = array_merge($atrg,
        [
            'sowing',
            [
                'attribute' => 'trays',
                'value' => function($model){
                    return Facil::limitarDecimales($model->trays);
                }
            ],
            [
                'attribute' => 'nursery_idnursery',
                'value' => $model->nurseryIdnursery->numcompartment,
            ],
            'sowedTable',
            [
                'attribute' => 'seedsReallyGerminated',
                'value' => function($model){
                    return Facil::limitarDecimales($model->seedsReallyGerminated);
                }
            ],
            [
                'attribute' => 'germinationReal',
                'value' => function($model){
                    return Facil::limitarDecimales($model->germinationReal);
                }
            ]
        ]);

    $three = DetailView::widget([
        'model' => $model,
        'attributes' => $atrthree,
    ]);

    $atrfour = array_merge($atrg,
        [
            'transplant',
            [
                'attribute' => 'nursery_idnursery1',
                'value' => $model->nurseryIdnursery1->numcompartment,
            ],
            'TidelFloor',
            [
                'label' => 'P. Nursery',
                'value' => function($model){
                    if ($model->FM == 'F'){
                        return $model->orderIdorder->nurseryF;
                    }else{
                        return $model->orderIdorder->nurseryM;
                    }
                }
            ],
            'remainTray',
        ]);

    $four = DetailView::widget([
        'model' => $model,
        'attributes' => $atrfour,
    ]);





        $fifth = DetailView::widget([
        'model' => $model,
        'attributes' => [
            'figure.figure',
            'remarks:ntext',
            'trasplantCompartment'
        ],
        ]);
    ?>

    <?= \kartik\tabs\TabsX::widget([
        'items' => [
            [
                'label' => 'Basic info',
                'content' => $one,
                'active' => true,
            ],
            [
                'label' => 'Start',
                'items' => [
                    [
                        'label' => 'Seed',
                        'content' => $two,
                    ],
                    [
                        'label' => 'Germination',
                        'content' => $two2,
                    ],
                ],
            ],
            [
                'label' => 'Sowing',
                'content' => $three,
            ],
            [
                'label' => 'Transplant',
                'content' => $four,
            ],
        ],
    ]);
    ?>
    <?=
    $fifth;
    ?>
</div>
