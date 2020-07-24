<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\seedsplanting\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Order'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'numCrop',
            'orderKg',
            'calculatedYield',
            'idorder',
            'numRows',
            // 'netNumOfPlantsF',
            // 'netNumOfPlantsM',
            // 'ReqDeliveryDate',
            // 'orderDate',
            // 'contractNumber',
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
            // 'pollenColectF',
            // 'pollenColectU',
            // 'pollenColectQ',
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
            // 'state',
            // 'action:ntext',
            // 'prueba:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
