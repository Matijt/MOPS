<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\FatherSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="father-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'idFather') ?>

    <?= $form->field($model, 'variety') ?>

    <?= $form->field($model, 'steril') ?>

    <?= $form->field($model, 'germination') ?>

    <?= $form->field($model, 'pollenProduction') ?>

    <?php // echo $form->field($model, 'tsw') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
