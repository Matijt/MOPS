<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $model backend\models\Stocklist */

$this->title = $model->harvestNumber;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stocklists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stocklist-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'idstocklist',
            'harvestNumber',
            'harvestDate',
            'numberOfFruitsHarvested',
            'cleaningDate',
            'wetSeedWeight',
            'drySeedWeight',
            [
                'attribute' => 'avgWeightOfSeedPF',
                'value' => function($model){
                    return Facil::limitarDecimales($model->avgWeightOfSeedPF);
                },
            ],
            'numberOfBags',
            'cartonNo',
            'shipmentDate',
            'packingListDescription',
            'remarksSeeds',
            'destroyed',
            [
                'attribute' => 'moisture',
                'value' => function($model){
                    return Facil::limitarDecimales($model->moisture);
                },
            ],
            'tsw',
            'eol',
            'status',
            'LUser',

        ],
    ]) ?>

</div>
