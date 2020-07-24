<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PollenSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pollen-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idpollen') ?>

    <?= $form->field($model, 'harvestWeek') ?>

    <?= $form->field($model, 'harvestDate') ?>

    <?= $form->field($model, 'harvestMl') ?>

    <?= $form->field($model, 'useWeek') ?>

    <?php // echo $form->field($model, 'useMl') ?>

    <?php // echo $form->field($model, 'youHaveMl') ?>

    <?php // echo $form->field($model, 'order_idorder') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
