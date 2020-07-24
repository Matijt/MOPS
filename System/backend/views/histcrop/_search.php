<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\HistcropSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="histcrop-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'idhistCrop') ?>

    <?= $form->field($model, 'Hybrid_idHybrid') ?>

    <?= $form->field($model, 'days') ?>

    <?= $form->field($model, 'createDate') ?>

    <?= $form->field($model, 'proces') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
