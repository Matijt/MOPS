<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Compartment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="compartment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'compNum')->textInput() ?>

    <?= $form->field($model, 'rowsNum')->textInput() ?>

    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
