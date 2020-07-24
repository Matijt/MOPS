<?php

use backend\models\StocklistSearch;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $model backend\models\StocklistHasOrder */

$this->title = "Totals of: ".$model->orderIdorder->hybridIdHybr->variety.", LN:".$model->lotNr;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stocklist Has Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stocklist-has-order-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Plants total',
                'value' => function($model){
                    if ($model->orderIdorder->realisedNrOfPlantsF != NULL && $model->orderIdorder->realisedNrOfPlantsF > 0){
                        return $model->orderIdorder->realisedNrOfPlantsF ;
                    }else{
                        return $model->orderIdorder->netNumOfPlantsF ;

                    }
                }
            ],
            'lotNr',
            'totalNumberOfFruitsHarvested',
            'totalWetSeedWeight',
            'totalDrySeedWeight',
            [
                'attribute' => 'totalAvarageWeightOfSeedsPF',
                'value' => function($model){
                    return Facil::limitarDecimales($model->totalAvarageWeightOfSeedsPF);
                },
            ],
            'totalNumberOfBags',
            'totalInStock',
            'totalShipped',
            [
                'attribute' => 'avarageGP',
                'value' => function($model){
                    return Facil::limitarDecimales($model->avarageGP);
                },
            ],
            'LUser',

        ],
    ]) ?>

            <p>
                <?php

                if (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
                    echo Html::button(Yii::t('app', 'Create Stocklist'), ['value' => 'index.php?r=stocklist/create&order='.$order,'class' => 'modalButtonCreate']);
                }
                ?>
            </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model){
            if($model->status == "Shipped"){
                return ['class' => 'success', 'style' => 'color: green;'];
            }else if($model->status == "In Stock"){
                return ['style' => 'color: yellow; background-color: #f1ae50'];
            }else{
                return ['class' => 'danger', 'style' => 'color: red;'];
            }
        },
        'columns' => [

//            'idstocklist',
            'harvestNumber',
            'numberOfFruitsHarvested',
            'packingListDescription',
            'cartonNo',
            //'cleaningDate',
            'wetSeedWeight',
            'drySeedWeight',
            'LUser',
//            'hasOrderId',
            // 'numberOfBags',
            // 'cartonNo',
            // 'shipmentDate',
            // 'packingListDescription',
            // 'remarksSeeds',
            // 'destroyed',
            // 'moisture',
            // 'tsw',
            // 'eol',
            // 'status',

            ['class' => 'yii\grid\ActionColumn',
                'template' => (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) ?
                    '{view}{update}':'{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=stocklist%2Fview&id='.$model->idstocklist), 'class' => 'modalButtonView btn btn-primary'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                            if (!(strpos($actual_link, 'page') !== false)) {
                                $edit = 'index.php?r=stocklist%2Fupdate&id=' . $model->idstocklist;
                            } else {
                                $edit = 'index.php?r=stocklist%2Fupdate&id=' . $model->idstocklist . "&page=" . $_GET['page'];
                            }
                            return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to($edit), 'class' => 'modalButtonEdit btn btn-primary'], [
                                'title' => Yii::t('app', 'Update'),
                            ]);
                    },

/*                    'delete' => function ($url, $model, $order) {
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'index.php?r=stocklist/delete&id='.$model->idstocklist."&order=".$model->hasOrderId, [
                            'data-method' => 'POST',
                            'title' => Yii::t('app', 'Update'),
                            'data-confirm' => "Are you sure to delete this item?",
                            'role' => 'button',
                            'class' => 'modalButtonDelete',
                        ]);
                    }*/
                ],
            ]
        ],
    ]); ?>


    <div class="row-">
        <div class="col-lg-6">
            <?php
            $datas = $dataProvider->query->all();
            $count = $dataProvider->query->count();
            $agp =0;
            $agf =0;
            foreach ($datas AS $data){
                $agp = $agp + $data->wetSeedWeight;
                $agf = $agf + $data->drySeedWeight;
            }
            if($count > 0 && $agp > 0 && $agf > 0){
                $agp = $agp/$count;
                $agf = $agf/$count;
            }
            echo '<label class="control-label" for="agp">Avarage Wet seeds Weight</label>';
            echo '<input type="text" class="form-control" name="agp" aria-invalid="true" value ="'.$agp.'">';
            echo "</div>";
            echo '<div class="col-lg-6">
            <label class="control-label" for="agf">Avarage Dry seed weight</label>
            <input type="text" id="agf" class="form-control" name="agf" aria-invalid="true" value="'.$agf.'">
            </div>';
            ?>
        </div>
        <div class="col-lg-4">
        </div>
    </div>
</div>
