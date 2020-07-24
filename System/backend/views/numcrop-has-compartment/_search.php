<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\NumcropHasCompartmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="numcrop-has-compartment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'numcrop_cropnum') ?>

    <?= $form->field($model, 'compartment_idCompartment') ?>

    <?= $form->field($model, 'createDate') ?>

    <?= $form->field($model, 'freeDate') ?>

    <?= $form->field($model, 'lastUpdatedDate') ?>

    <?php // echo $form->field($model, 'rowsOccupied') ?>

    <?php // echo $form->field($model, 'rowsLeft') ?>

    <?php // echo $form->field($model, 'crop_idcrops') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
