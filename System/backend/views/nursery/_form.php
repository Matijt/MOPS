<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Nursery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nursery-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'numcompartment')->textInput() ?>

    <?= $form->field($model, 'tablesFloors')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
