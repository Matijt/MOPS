<?php

use yii\helpers\Html;
    use yii\grid\GridView;
//use kartik\grid\GridView;
use yii\helpers\Url;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StocklistHasOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stocklists');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stocklist-has-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php
        if (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
            echo Html::button(Yii::t('app', 'Create Full Stocklist'), ['value' => 'index.php?r=stocklist-has-order/create','class' => 'modalButtonCreate']);
        } ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'Variety',
                'value' => 'orderIdorder.hybridIdHybr.variety',
                'attribute' => 'totalDrySeedWeight',
                'headerOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
                'contentOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
            ],
            [
                'label' => 'Compartment',
                'attribute' => 'order_idorder',
                'value' => 'orderIdorder.compartmentIdCompartment.compNum',
                'headerOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
                'contentOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
            ],
            [
                'label' => 'Crop',
                'attribute' => 'totalShipped',
                'value' => 'orderIdorder.numCrop',
                'headerOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
                'contentOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
            ],

            [
                'attribute' => 'totalAvarageWeightOfSeedsPF',
                'value' => function($model){
                    return Facil::limitarDecimales($model->totalAvarageWeightOfSeedsPF);
                },
                'label' => 'Total Avarage Weight Of Seeds Per fruits',
                'headerOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
                'contentOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
            ],
            [
                'attribute' => 'avarageGP',
                'value' => function($model){
                    return Facil::limitarDecimales($model->avarageGP);
                },
                'headerOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
                'contentOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
            ],
            [
                'attribute' => 'totalNumberOfFruitsHarvested',
                'value' => 'totalNumberOfFruitsHarvested',
                'headerOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
                'contentOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
            ],
            [
                'attribute' => 'totalWetSeedWeight',
                'value' => 'totalWetSeedWeight',
                'headerOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
                'contentOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
            ],
            [
                'attribute' => 'LUser',
                'value' => 'LUser',
                'headerOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
                'contentOptions' => [
                    'style' => 'text-align:center;vertical-align:middle;'
                ],
            ],
            // 'totalDrySeedWeight',
            // 'totalNumberOfBags',
            // 'totalInStock',
            // 'orderIdorder.numCrop',

            ['class' => 'yii\grid\ActionColumn',
//                'header' => 'Actions',
                'template' => (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) ?
                    '{view}{update}':'{view}',
                'buttons' => [

                    'update' => function ($url, $model) {
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        if (!(strpos($actual_link, 'page') !== false)){
                            $edit = 'index.php?r=stocklist-has-order%2Fupdate&idstocklist_has_order='.$model->idstocklist_has_order.'&stocklist_idstocklist='.$model->stocklist_idstocklist.'&order_idorder='.$model->order_idorder;
                        }else{
                            $edit = 'index.php?r=stocklist-has-order%2Fupdate&idstocklist_has_order='.$model->idstocklist_has_order.'&stocklist_idstocklist='.$model->stocklist_idstocklist.'&order_idorder='.$model->order_idorder."&page=".$_GET['page'];
                        }
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to('index.php?r=stocklist-has-order%2Fupdate&idstocklist_has_order='.$model->idstocklist_has_order.'&stocklist_idstocklist='.$model->stocklist_idstocklist.'&order_idorder='.$model->order_idorder), 'class' => 'modalButtonEdit btn btn-primary'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },

/*                    'delete' => function ($url, $model) {
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        if (!(strpos($actual_link, 'page') !== false)){
                            $edit = 'index.php?r=stocklist-has-order/delete&idstocklist_has_order='.$model->idstocklist_has_order.'&stocklist_idstocklist='.$model->stocklist_idstocklist.'&order_idorder='.$model->order_idorder;
                        }else{
                            $edit = 'index.php?r=stocklist-has-order/delete&idstocklist_has_order='.$model->idstocklist_has_order.'&stocklist_idstocklist='.$model->stocklist_idstocklist.'&order_idorder='.$model->order_idorder."&page=".$_GET['page'];
                        }
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $edit, [
                            'data-method' => 'POST',
                            'title' => Yii::t('app', 'Update'),
                            'data-confirm' => "Are you sure to delete this item?",
                            'role' => 'button',
                            'class' => 'modalButtonDelete',
                        ]);
                    }*/
                ],
            ],
        ],
        'options' => [
            'style' => 'overflow: auto; word-wrap: break-word;',
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
                $agp = $agp + $data->avarageGP;
                $agf = $agf + $data->totalAvarageWeightOfSeedsPF;
            }
            if($count > 0 && $agp > 0 && $agf > 0){
                $agp = $agp/$count;
                $agf = $agf/$count;
            }
            echo '<label class="control-label" for="agp">Avarage grams per plant</label>';
            echo '<input type="text" class="form-control" name="agp" aria-invalid="true" value ="'.$agp.'">';
            echo "</div>";
            echo '<div class="col-lg-6">
            <label class="control-label" for="agf">Avarage grams per fruit</label>
            <input type="text" id="agf" class="form-control" name="agf" aria-invalid="true" value="'.$agf.'">
            </div>';
            ?>
        </div>
        <div class="col-lg-4">
        </div>
    </div>
</div>


<style>
    .centered{
        text-align: center;
    }
    .glyphicon-eye-open{

        display: inline-block;
        padding: 6px 12px;
        margin-bottom: 1px;
        font-size: 14px;
        font-weight: normal;
        line-height: 1.02857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-image: none;
        border: 1px solid transparent;
        border-radius: 4px;

        color: #fff;
        background-color: #5bc0de;
        border-color: #2e6da4;

    }
</style>
