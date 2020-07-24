<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\Historialcomp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="historialcomp-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class='col-lg-3'>
            <?= $form->field($model, 'crop')->textInput(['type' => 'number'])?>
        </div>
        <div class='col-lg-3'>
            <?= $form->field($model, 'title')->textInput() ?>
        </div>
        <div class='col-lg-3'>
            <?= $form->field($model, 'compartment_idCompartment')->dropDownList(
                ArrayHelper::map(\backend\models\Compartment::find()->all(), 'idCompartment', 'compNum'),
                [
                    'prompt' => 'Select Compartment'
                ]
            ) ?>
        </div>
        <div class='col-lg-3'>
            <?= $form->field($model, 'date')->widget(
                DatePicker::className(), [
                // inline too, not bad
//        'inline' => true,
                'language' => 'en_EN',
                'value' => 'dd-mm-yyyy',
                // modify template for custom rendering
//        'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]); ?>
        </div>
    </div>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
