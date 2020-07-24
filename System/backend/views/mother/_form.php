<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Mother */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mother-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'variety')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'steril')->dropDownList(array('100'=>'No', '50'=>'Yes')) ?>

    <?php
    if($model->isNewRecord) {
        echo $form->field($model, 'germination')->textInput(['value' => '100']);
    }else{
        echo $form->field($model, 'germination')->textInput();
    }
    ?>

    <?= $form->field($model, 'gP')->textInput() ?>

    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
