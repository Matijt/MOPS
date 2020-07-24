<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\RegistrynurserySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="registrynursery-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'numPM') ?>

    <?= $form->field($model, 'germination') ?>

    <?= $form->field($model, 'sowedSeeds') ?>

    <?= $form->field($model, 'recievedSeeds') ?>

    <?php // echo $form->field($model, 'germinatedSeeds') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <?php // echo $form->field($model, 'nursery_idnursery') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
