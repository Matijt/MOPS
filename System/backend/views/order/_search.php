<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idorder') ?>

    <?= $form->field($model, 'numRows') ?>

    <?= $form->field($model, 'netNumOfPlantsF') ?>

    <?= $form->field($model, 'netNumOfPlantsM') ?>

    <?= $form->field($model, 'ReqDeliveryDate') ?>

    <?php // echo $form->field($model, 'orderDate') ?>

    <?php // echo $form->field($model, 'calculatedYield') ?>

    <?php // echo $form->field($model, 'contractNumber') ?>

    <?php // echo $form->field($model, 'ssRecDate') ?>

    <?php // echo $form->field($model, 'sowingM') ?>

    <?php // echo $form->field($model, 'sowingF') ?>

    <?php // echo $form->field($model, 'nurseryM') ?>

    <?php // echo $form->field($model, 'nurseryF') ?>

    <?php // echo $form->field($model, 'check') ?>

    <?php // echo $form->field($model, 'sowingDateM') ?>

    <?php // echo $form->field($model, 'sowingDateF') ?>

    <?php // echo $form->field($model, 'realisedNrOfPlantsM') ?>

    <?php // echo $form->field($model, 'realisedNrOfPlantsF') ?>

    <?php // echo $form->field($model, 'transplantingM') ?>

    <?php // echo $form->field($model, 'transplantingF') ?>

    <?php // echo $form->field($model, 'extractedPlants') ?>

    <?php // echo $form->field($model, 'remainingPlants') ?>

    <?php // echo $form->field($model, 'pollinationF') ?>

    <?php // echo $form->field($model, 'pollinationU') ?>

    <?php // echo $form->field($model, 'harvestF') ?>

    <?php // echo $form->field($model, 'harvestU') ?>

    <?php // echo $form->field($model, 'steamDesinfectionF') ?>

    <?php // echo $form->field($model, 'steamDesinfectionU') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <?php // echo $form->field($model, 'compartment_idCompartment') ?>

    <?php // echo $form->field($model, 'plantingDistance') ?>

    <?php // echo $form->field($model, 'Hybrid_idHybrid') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
