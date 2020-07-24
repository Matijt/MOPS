<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\StocklistHasOrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stocklist-has-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idstocklist_has_order') ?>

    <?= $form->field($model, 'stocklist_idstocklist') ?>

    <?= $form->field($model, 'order_idorder') ?>

    <?= $form->field($model, 'totalNumberOfFruitsHarvested') ?>

    <?= $form->field($model, 'totalWetSeedWeight') ?>

    <?php // echo $form->field($model, 'totalDrySeedWeight') ?>

    <?php // echo $form->field($model, 'totalAvarageWeightOfSeedsPF') ?>

    <?php // echo $form->field($model, 'totalNumberOfBags') ?>

    <?php // echo $form->field($model, 'totalInStock') ?>

    <?php // echo $form->field($model, 'totalShipped') ?>

    <?php // echo $form->field($model, 'avarageGP') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
