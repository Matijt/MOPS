<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EstimationsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="estimations-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idestimations') ?>

    <?= $form->field($model, 'totalFemalesCount') ?>

    <?= $form->field($model, 'totalPlantsCheked') ?>

    <?= $form->field($model, 'inStock') ?>

    <?= $form->field($model, 'fruitsHarvest') ?>

    <?php // echo $form->field($model, 'plantsTotal') ?>

    <?php // echo $form->field($model, 'gramPerFruit') ?>

    <?php // echo $form->field($model, 'fruitsInPlant') ?>

    <?php // echo $form->field($model, 'gramsInPlant') ?>

    <?php // echo $form->field($model, 'totalHarvestS') ?>

    <?php // echo $form->field($model, 'pollinationDays') ?>

    <?php // echo $form->field($model, 'fruitsAvgPerDay') ?>

    <?php // echo $form->field($model, 'extraPollination') ?>

    <?php // echo $form->field($model, 'fruitsToBeSetted') ?>

    <?php // echo $form->field($model, 'gramsToBeSetted') ?>

    <?php // echo $form->field($model, 'gramsRealToBeSetted') ?>

    <?php // echo $form->field($model, 'totalHarvest') ?>

    <?php // echo $form->field($model, 'difference') ?>

    <?php // echo $form->field($model, 'avgGrsPlant') ?>

    <?php // echo $form->field($model, 'totalEstimatedProduction') ?>

    <?php // echo $form->field($model, 'order_idorder') ?>

    <?php // echo $form->field($model, 'fecha') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
