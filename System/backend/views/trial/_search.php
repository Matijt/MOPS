<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\TrialSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trial-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_trial') ?>

    <?= $form->field($model, 'reason') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'observations') ?>

    <?= $form->field($model, 'numRows') ?>

    <?php // echo $form->field($model, 'compartment_idCompartment') ?>

    <?php // echo $form->field($model, 'numCrop') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
