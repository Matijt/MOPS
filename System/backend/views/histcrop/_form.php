<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Histcrop */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="histcrop-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Hybrid_idHybrid')->textInput() ?>

    <?= $form->field($model, 'days')->textInput() ?>

    <?= $form->field($model, 'createDate')->textInput() ?>

    <?= $form->field($model, 'proces')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
