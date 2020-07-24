<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\seedsplanting\models\Order */

$this->title = $model->idorder;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->idorder], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->idorder], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'numCrop',
            'orderKg',
            'calculatedYield',
            'idorder',
            'numRows',
            'netNumOfPlantsF',
            'netNumOfPlantsM',
            'ReqDeliveryDate',
            'orderDate',
            'contractNumber',
            'ssRecDate',
            'sowingM',
            'sowingF',
            'nurseryM',
            'nurseryF',
            'check',
            'sowingDateM',
            'sowingDateF',
            'realisedNrOfPlantsM',
            'realisedNrOfPlantsF',
            'transplantingM',
            'transplantingF',
            'extractedPlantsF',
            'extractedPlantsM',
            'remainingPlantsF',
            'remainingPlantsM',
            'pollenColectF',
            'pollenColectU',
            'pollenColectQ',
            'pollinationF',
            'pollinationU',
            'harvestF',
            'harvestU',
            'steamDesinfectionF',
            'steamDesinfectionU',
            'remarks:ntext',
            'compartment_idCompartment',
            'plantingDistance',
            'Hybrid_idHybrid',
            'state',
            'action:ntext',
            'prueba:ntext',
        ],
    ]) ?>

</div>
