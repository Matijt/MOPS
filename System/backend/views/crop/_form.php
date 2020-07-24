<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Crop */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="crop-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'crop')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sowingFemale')->textInput() ?>

    <?= $form->field($model, 'transplantingMale')->textInput() ?>

    <?= $form->field($model, 'transplantingFemale')->textInput() ?>

    <?= $form->field($model, 'pollenColectF')->textInput() ?>

    <?= $form->field($model, 'pollenColectU')->textInput() ?>

    <?= $form->field($model, 'pollinitionF')->textInput() ?>

    <?= $form->field($model, 'pollinitionU')->textInput() ?>

    <?= $form->field($model, 'harvestF')->textInput() ?>

    <?= $form->field($model, 'harvestU')->textInput() ?>

    <?= $form->field($model, 'steamDesinfection')->textInput() ?>

    <?= $form->field($model, 'durationOfTheCrop')->textInput() ?>

    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
