<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\HybridSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hybrid-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'idHybrid') ?>

    <?= $form->field($model, 'variety') ?>

    <?= $form->field($model, 'sowingFemale') ?>

    <?= $form->field($model, 'transplantingMale') ?>

    <?= $form->field($model, 'transplantingFemale') ?>

    <?php // echo $form->field($model, 'pollenColectF') ?>

    <?php // echo $form->field($model, 'pollenColectU') ?>

    <?php // echo $form->field($model, 'pollinitionF') ?>

    <?php // echo $form->field($model, 'pollinitionU') ?>

    <?php // echo $form->field($model, 'harvestF') ?>

    <?php // echo $form->field($model, 'harvestU') ?>

    <?php // echo $form->field($model, 'steamDesinfection') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <?php // echo $form->field($model, 'Crop_idcrops') ?>

    <?php // echo $form->field($model, 'Father_idFather') ?>

    <?php // echo $form->field($model, 'Mother_idMother') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
