<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = $model->ReqDeliveryDate . " " . $model->hybridIdHybr->variety;;
if(isset($_GET['name'])){
    $name = $_GET['name'];
}
$this->title = Yii::t('app', 'Update {modelClass}: ', [
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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'numCrop',
            'orderKg',
            [
                'attribute' => 'compartment_idCompartment',
                'value' => $model->compartmentIdCompartment->compNum,
            ],
            ['attribute' => 'Hybrid_idHybrid',
                'value' => $model->hybridIdHybr->variety,
            ],
            'calculatedYield',
//            'idorder',
            'numRows',
            'contractNumber',
            'ReqDeliveryDate',
            'orderDate',
            'ssRecDate',
            'netNumOfPlantsF',
            'netNumOfPlantsM',
            'sowingF',
            'sowingM',
            'nurseryF',
            'nurseryM',
            'realisedNrOfPlantsM',
            'extractedPlantsM',
            'remainingPlantsM',
            'realisedNrOfPlantsF',
            'extractedPlantsF',
            'remainingPlantsF',
            'check',
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
            'remarks:ntext'

        ],
    ]) ?>

</div>
