<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\StocklistSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stocklist-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idstocklist') ?>

    <?= $form->field($model, 'harvestNumber') ?>

    <?= $form->field($model, 'harvestDate') ?>

    <?= $form->field($model, 'numberOfFruitsHarvested') ?>

    <?= $form->field($model, 'cumulativeNumberOfFruitesHarvested') ?>

    <?php // echo $form->field($model, 'cleaningDate') ?>

    <?php // echo $form->field($model, 'wetSeedWeight') ?>

    <?php // echo $form->field($model, 'drySeedWeight') ?>

    <?php // echo $form->field($model, 'comulativeDrySeedWeight') ?>

    <?php // echo $form->field($model, 'avgWeightOfSeedPF') ?>

    <?php // echo $form->field($model, 'numberOfBags') ?>

    <?php // echo $form->field($model, 'cartonNo') ?>

    <?php // echo $form->field($model, 'shipmentDate') ?>

    <?php // echo $form->field($model, 'packingListDescription') ?>

    <?php // echo $form->field($model, 'remarksSeeds') ?>

    <?php // echo $form->field($model, 'destroyed') ?>

    <?php // echo $form->field($model, 'moisture') ?>

    <?php // echo $form->field($model, 'tsw') ?>

    <?php // echo $form->field($model, 'eol') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
