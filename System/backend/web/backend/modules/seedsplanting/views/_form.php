<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\seedsplanting\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'numCrop')->textInput() ?>

    <?= $form->field($model, 'orderKg')->textInput() ?>

    <?= $form->field($model, 'calculatedYield')->textInput() ?>

    <?= $form->field($model, 'numRows')->textInput() ?>

    <?= $form->field($model, 'netNumOfPlantsF')->textInput() ?>

    <?= $form->field($model, 'netNumOfPlantsM')->textInput() ?>

    <?= $form->field($model, 'ReqDeliveryDate')->textInput() ?>

    <?= $form->field($model, 'orderDate')->textInput() ?>

    <?= $form->field($model, 'contractNumber')->textInput() ?>

    <?= $form->field($model, 'ssRecDate')->textInput() ?>

    <?= $form->field($model, 'sowingM')->textInput() ?>

    <?= $form->field($model, 'sowingF')->textInput() ?>

    <?= $form->field($model, 'nurseryM')->textInput() ?>

    <?= $form->field($model, 'nurseryF')->textInput() ?>

    <?= $form->field($model, 'check')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sowingDateM')->textInput() ?>

    <?= $form->field($model, 'sowingDateF')->textInput() ?>

    <?= $form->field($model, 'realisedNrOfPlantsM')->textInput() ?>

    <?= $form->field($model, 'realisedNrOfPlantsF')->textInput() ?>

    <?= $form->field($model, 'transplantingM')->textInput() ?>

    <?= $form->field($model, 'transplantingF')->textInput() ?>

    <?= $form->field($model, 'extractedPlantsF')->textInput() ?>

    <?= $form->field($model, 'extractedPlantsM')->textInput() ?>

    <?= $form->field($model, 'remainingPlantsF')->textInput() ?>

    <?= $form->field($model, 'remainingPlantsM')->textInput() ?>

    <?= $form->field($model, 'pollenColectF')->textInput() ?>

    <?= $form->field($model, 'pollenColectU')->textInput() ?>

    <?= $form->field($model, 'pollenColectQ')->textInput() ?>

    <?= $form->field($model, 'pollinationF')->textInput() ?>

    <?= $form->field($model, 'pollinationU')->textInput() ?>

    <?= $form->field($model, 'harvestF')->textInput() ?>

    <?= $form->field($model, 'harvestU')->textInput() ?>

    <?= $form->field($model, 'steamDesinfectionF')->textInput() ?>

    <?= $form->field($model, 'steamDesinfectionU')->textInput() ?>

    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'compartment_idCompartment')->textInput() ?>

    <?= $form->field($model, 'plantingDistance')->textInput() ?>

    <?= $form->field($model, 'Hybrid_idHybrid')->textInput() ?>

    <?= $form->field($model, 'state')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'action')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'prueba')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
