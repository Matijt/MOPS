<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $model backend\models\Estimations */

$this->title = $model->orderIdorder->hybridIdHybr->variety;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Estimations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$model->orderIdorder->pollinationF = date('d-m-Y', strtotime($model->orderIdorder->pollinationF));
$model->orderIdorder->pollinationU = date('d-m-Y', strtotime($model->orderIdorder->pollinationU));
$model->fecha = date('d-m-Y', strtotime($model->fecha));
?>
<div class="estimations-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php

    if(substr($model->orderIdorder->hybridIdHybr->variety, 0, 1) == "T") {

        echo \kartik\tabs\TabsX::widget([
            'items' => [
                [
                    'label' => 'Input',
                    'content' =>
                        DetailView::widget([
                            'model' => $model,
                            'attributes' =>
                                [
                                    'totalFemalesCount',
                                    'totalPlantsCheked',
                                    'inStock',
                                    'fruitsHarvest',
                                    'plantsTotal',
                                    'fecha',
                                    //'LUser',
                                ],
                        ]),
                    'active' => true
                ],
                [
                    'label' => 'Setted in plant',
                    'content' =>
                        DetailView::widget([
                            'model' => $model,
                            'attributes' =>
                                [
                                    [
                                        'attribute' => 'gramPerFruit',
                                        'value' => function($model){
                                            return Facil::limitarDecimales($model->gramPerFruit);
                                        }
                                    ],
                                    'fruitsInPlant',
                                    [
                                        'attribute' => 'gramsInPlant',
                                        'value' => function($model){
                                            return Facil::limitarDecimales($model->gramsInPlant);
                                        }
                                    ],
                                    [
                                        'attribute' => 'totalHarvestS',
                                        'value' => function($model){
                                            return Facil::limitarDecimales($model->totalHarvestS);
                                        }
                                    ],
                                    'LUser',
                                ],
                        ]),
                ],
                [
                    'label' => 'To be setted',
                    'content' =>
                        DetailView::widget([
                            'model' => $model,
                            'attributes' =>
                                [
                                    'orderIdorder.pollinationF',
                                    'pollinationDays',
                                    [
                                        'attribute' => 'fruitsAvgPerDay',
                                        'value' => function($model){
                                            return Facil::limitarDecimales($model->fruitsAvgPerDay);
                                        }
                                    ],
                                    'extraPollination',
                                    'orderIdorder.pollinationU',
                                    [
                                        'attribute' => 'fruitsToBeSetted',
                                        'value' => function($model){
                                            return Facil::limitarDecimales($model->fruitsToBeSetted);
                                        }
                                    ],
                                    [
                                        'attribute' => 'gramsToBeSetted',
                                        'value' => function($model){
                                            return Facil::limitarDecimales($model->gramsToBeSetted);
                                        }
                                    ],
                                    [
                                        'attribute' => 'gramsRealToBeSetted',
                                        'value' => function($model){
                                            return Facil::limitarDecimales($model->gramsRealToBeSetted);
                                        }
                                    ],
                                    'LUser',
                                ],
                        ]),
                ],
                [
                    'label' => 'Totals',
                    'content' =>
                        DetailView::widget([
                            'model' => $model,
                            'attributes' =>
                                [
                                    [
                                        'attribute' => 'totalHarvest',
                                        'value' => function($model){
                                            return Facil::limitarDecimales($model->totalHarvest);
                                        }
                                    ],
                                    [
                                        'attribute' => 'orderKg',
                                        'value' => function($model){
                                            return Facil::limitarDecimales($model->orderIdorder->orderKg);
                                        }
                                    ],
                                    [
                                        'attribute' => 'difference',
                                        'value' => function ($model) {
                                            return Facil::limitarDecimales($model->difference) . "%";
                                        },
                                    ],
                                    [
                                        'attribute' => 'avgGrsPlant',
                                        'value' => function($model){
                                            return Facil::limitarDecimales($model->avgGrsPlant);
                                        }
                                    ],
                                    [
                                        'attribute' => 'totalEstimatedProduction',
                                        'value' => function($model){
                                            return Facil::limitarDecimales($model->totalEstimatedProduction);
                                        }
                                    ],
                                    'LUser',
                                ],
                        ]),
                ],
            ],
        ]);
    }else{

         \kartik\tabs\TabsX::widget([
            'items' => [
                [
                    'label' => 'Set 1',
                    'content' =>
                        DetailView::widget([
                            'model' => $model,
                            'attributes' =>
                                [
                                    'orderIdorder.orderKg',
                                    [
                                        'label' => 'Num Rows',
                                        'attribute' => function($model){
                                            if ($model->orderIdorder->numRowsOpt != null) {
                                                return $model->orderIdorder->numRowsOpt;
                                            }else{
                                                return $model->orderIdorder->numRows;
                                            }
                                        }
                                    ],
                                    'totalPlantsCheked',
                                    'totalFemalesCount',
                                    'avgFruits1',
                                    'fruitsEstimated1',
                                    'gramPerFruit',
                                    'gramsEstimated1',
                                    'gramsSet1',
                                    [
                                        'label' => 'Difference In %',
                                        'attribute' => function($model){
                                            $num = (($model->gramsEstimated1-$model->gramsSet1)/$model->gramsEstimated1)*100;
                                            return $num."%";
                                        }
                                    ],
                                    [
                                        'label' => 'Difference In Grams',
                                        'attribute' => function($model){
                                            return ($model->gramsEstimated1-$model->gramsSet1);
                                        }
                                    ]
                                ],
                        ]),
                    'active' => true
                ],
                [
                    'label' => 'Set 2',
                    'content' =>
                        DetailView::widget([
                            'model' => $model,
                            'attributes' =>
                                [

                                    'orderIdorder.orderKg',
                                    [
                                        'label' => 'Num Rows',
                                        'attribute' => function($model){
                                            if ($model->orderIdorder->numRowsOpt != null) {
                                                return $model->orderIdorder->numRowsOpt;
                                            }else{
                                                return $model->orderIdorder->numRows;
                                            }
                                        }
                                    ],
                                    'totalPlantsCheked',
                                    'totalFemalesCount2',
                                    'avgFruits2',
                                    'fruitsEstimated2',
                                    'gramPerFruit2',
                                    'gramsEstimated2',
                                    'gramsSet2',
                                    [
                                        'label' => 'Difference In %',
                                        'attribute' => function($model){
                                            if($model->gramsEstimated2){
                                                $num = (($model->gramsEstimated2-$model->gramsSet2)/$model->gramsEstimated2)*100;
                                                return $num."%";
                                            }else{
                                                return 0;
                                            }
                                        }
                                    ],
                                    [
                                        'label' => 'Difference In Grams',
                                        'attribute' => function($model){
                                            return ($model->gramsEstimated2-$model->gramsSet2);
                                        }
                                    ]
                                ],
                        ]),
                ],
                [
                    'label' => 'Real',
                    'content' =>
                        DetailView::widget([
                            'model' => $model,
                            'attributes' =>
                                [
                                    'orderIdorder.orderKg',
                                    [
                                        'label' => 'Num Rows',
                                        'attribute' => function($model){
                                            if ($model->orderIdorder->numRowsOpt != null) {
                                                return $model->orderIdorder->numRowsOpt;
                                            }else{
                                                return $model->orderIdorder->numRows;
                                            }
                                        }
                                    ],
                                    'avgGrsPlant',
                                    'gramsEstimated3',
                                    'gramsSetFinal',
                                    [
                                        'label' => 'Difference In %',
                                        'attribute' => function($model){
                                            if ($model->gramsEstimated3){
                                                $num = (($model->gramsEstimated3-$model->gramsSetFinal)/$model->gramsEstimated3)*100;
                                                return $num."%";
                                            }else{
                                                return 0;
                                            }
                                        }
                                    ],
                                    [
                                        'label' => 'Difference In Grams',
                                        'attribute' => function($model){
                                            return ($model->gramsEstimated3-$model->gramsSetFinal);
                                        }
                                    ]
                                ],
                        ]),
                ],
            ],
        ]);
        ?>
        <style>
            th{
                text-align: center;
            }
            td{
                text-align: center;
            }
        </style>
        <table class="table table-striped table-bordered detail-view">
            <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">First Set</th>
                <th scope="col">Second Set</th>
                <th scope="col">Real</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="col">Order</th>
                <td scope="col" colspan="3" align="center"><?= $model->orderIdorder->orderKg ?></td>
            </tr>
            <tr>
                <th scope="col">Num Rows</th>
                <td scope="col" colspan="3" align="center"><?= $model->orderIdorder->numRows ?></td>
            </tr>
            <tr>
                <th scope="col">Num of females</th>
                <td scope="col" colspan="3" align="center"><?= $model->plantsTotal; ?></td>
            </tr>
            <tr>
                <th scope="col">Total Plants Checked</th>
                <td scope="col" colspan="2" align="center"><?= $model->totalPlantsCheked ?></td>
                <td scope="col" colspan="1" align="center"></td>
            </tr>
            <tr>
                <th scope="col">Total Fruits Count</th>
                <td scope="col" colspan="1" align="center"><?= $model->totalFemalesCount ?></td>
                <td scope="col" colspan="1" align="center"><?= $model->totalFemalesCount2 ?></td>
                <td scope="col" colspan="1" align="center"></td>
            </tr>
            <tr>
                <th scope="col">Fruits Estimated</th>
                <td scope="col" colspan="1" align="center"><?= round($model->fruitsEstimated1, 2); ?></td>
                <td scope="col" colspan="1" align="center"><?= round($model->fruitsEstimated2, 2) ?></td>
                <td scope="col" colspan="1" align="center"></td>
            </tr>
            <tr>
                <th scope="col">Avg Fruits</th>
                <td scope="col" colspan="1" align="center"><?= round($model->avgFruits1, 2); ?></td>
                <td scope="col" colspan="1" align="center"><?= round($model->avgFruits2, 2) ?></td>
                <td scope="col" colspan="1" align="center"></td>
            </tr>
            <tr>
                <th scope="col">Gram Per Fruit</th>
                <td scope="col" colspan="1" align="center"><?= round($model->gramPerFruit, 2); ?></td>
                <td scope="col" colspan="1" align="center"><?= round($model->gramPerFruit2, 2) ?></td>
                <td scope="col" colspan="1" align="center"></td>
            </tr>
            <tr>
                <th scope="col">Grams Estimated</th>
                <td scope="col" colspan="1" align="center"><?= round($model->gramsEstimated1, 2); ?></td>
                <td scope="col" colspan="1" align="center"><?= round($model->gramsEstimated2, 2) ?></td>
                <td scope="col" colspan="1" align="center"><?= round($model->gramsEstimated3, 2) ?></td>
            </tr>
            <tr>
                <th scope="col">Grams Real</th>
                <td scope="col" colspan="1" align="center"><?= round($model->gramsSet1, 2); ?></td>
                <td scope="col" colspan="1" align="center"><?= round($model->gramsSet2, 2) ?></td>
                <td scope="col" colspan="1" align="center"><?= round($model->gramsSetFinal, 2) ?></td>
            </tr>
            <tr>
                <th scope="col">Difference in %</th>
                <td scope="col" colspan="1" align="center"><?php if($model->gramsEstimated1 && $model->gramsSet1){echo round((($model->gramsEstimated1-$model->gramsSet1)/$model->gramsEstimated1)*100, 2);}else{echo 0;} ?>%</td>
                <td scope="col" colspan="1" align="center"><?php if($model->gramsEstimated2 && $model->gramsSet2){echo round((($model->gramsEstimated2-$model->gramsSet2)/$model->gramsEstimated2)*100, 2);}else{echo 0;} ?>%</td>
                <td scope="col" colspan="1" align="center"><?php if($model->gramsEstimated3 && $model->gramsSetFinal){echo round((($model->gramsEstimated3-$model->gramsSetFinal)/$model->gramsEstimated3)*100, 2);}else{echo 0;} ?>%</td>
            </tr>
            <tr>
                <th scope="col">Difference in grams</th>
                <td scope="col" colspan="1" align="center"><?php if($model->gramsEstimated1 && $model->gramsSet1){echo round(($model->gramsEstimated1-$model->gramsSet1), 2);}else{echo 0;} ?></td>
                <td scope="col" colspan="2" align="center"><?php if($model->gramsEstimated2 && $model->gramsSet2){echo round(($model->gramsEstimated2-$model->gramsSet2), 2);}else{echo 0;} ?></td>
            </tr>
            <tr>
                <th scope="col" colspan="3" align="center">Grams/Plant</th>
                <td scope="col" colspan="1" align="center"><?= round($model->avgGrsPlant, 2) ?></td>
            </tr>
            <tr>
                <th scope="col" colspan="3" align="center">Last User</th>
                <td scope="col" colspan="1" align="center"><?= $model->LUser ?></td>
            </tr>
            </tbody>
        </table>
        <?php

    }
    ?>

</div>
