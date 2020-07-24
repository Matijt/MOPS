<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */
/* @var $form ActiveForm */
?>
<div class="order">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'numCrop') ?>
        <?= $form->field($model, 'orderKg') ?>
        <?= $form->field($model, 'numRows') ?>
        <?= $form->field($model, 'pollenColectF') ?>
        <?= $form->field($model, 'pollenColectU') ?>
        <?= $form->field($model, 'compartment_idCompartment') ?>
        <?= $form->field($model, 'plantingDistance') ?>
        <?= $form->field($model, 'Hybrid_idHybrid') ?>
        <?= $form->field($model, 'state') ?>
        <?= $form->field($model, 'nursery_idnursery') ?>
        <?= $form->field($model, 'netNumOfPlantsF') ?>
        <?= $form->field($model, 'netNumOfPlantsM') ?>
        <?= $form->field($model, 'contractNumber') ?>
        <?= $form->field($model, 'sowingM') ?>
        <?= $form->field($model, 'sowingF') ?>
        <?= $form->field($model, 'nurseryM') ?>
        <?= $form->field($model, 'nurseryF') ?>
        <?= $form->field($model, 'realisedNrOfPlantsM') ?>
        <?= $form->field($model, 'realisedNrOfPlantsF') ?>
        <?= $form->field($model, 'extractedPlantsF') ?>
        <?= $form->field($model, 'extractedPlantsM') ?>
        <?= $form->field($model, 'remainingPlantsF') ?>
        <?= $form->field($model, 'remainingPlantsM') ?>
        <?= $form->field($model, 'pollenColectQ') ?>
        <?= $form->field($model, 'calculatedYield') ?>
        <?= $form->field($model, 'gpOrder') ?>
        <?= $form->field($model, 'ReqDeliveryDate') ?>
        <?= $form->field($model, 'orderDate') ?>
        <?= $form->field($model, 'ssRecDate') ?>
        <?= $form->field($model, 'sowingDateM') ?>
        <?= $form->field($model, 'sowingDateF') ?>
        <?= $form->field($model, 'transplantingM') ?>
        <?= $form->field($model, 'transplantingF') ?>
        <?= $form->field($model, 'pollinationF') ?>
        <?= $form->field($model, 'pollinationU') ?>
        <?= $form->field($model, 'harvestF') ?>
        <?= $form->field($model, 'harvestU') ?>
        <?= $form->field($model, 'steamDesinfectionF') ?>
        <?= $form->field($model, 'steamDesinfectionU') ?>
        <?= $form->field($model, 'action') ?>
        <?= $form->field($model, 'prueba') ?>
        <?= $form->field($model, 'remarks') ?>
        <?= $form->field($model, 'selector') ?>
        <?= $form->field($model, 'check') ?>
    
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- order -->
