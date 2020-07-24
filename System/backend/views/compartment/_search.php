<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CompartmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="compartment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'idCompartment') ?>

    <?= $form->field($model, 'compNum') ?>

    <?= $form->field($model, 'rowsNum') ?>

    <?= $form->field($model, 'grossSurface') ?>

    <?= $form->field($model, 'netSurface') ?>

    <?php // echo $form->field($model, 'grossLength') ?>

    <?php // echo $form->field($model, 'netLength') ?>

    <?php // echo $form->field($model, 'width') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
