<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Mother */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mother-form">

    <?php $form = ActiveForm::begin();
    $model->rfselectorc = '';?>


    <?= $form->field($model, 'rfselectorc')->textarea(['rows' => 6, 'required' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Change'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
