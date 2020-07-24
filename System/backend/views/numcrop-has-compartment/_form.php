<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Compartment;
use backend\models\Numcrop;
use backend\models\Crop;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\NumcropHasCompartment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="numcrop-has-compartment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'crop_idcrops')->dropDownList(
        ArrayHelper::map(Crop::find()->andFilterWhere(["!=", "idcrops",2])->all(), 'idcrops', 'crop'),
        ['prompt' => 'Select the crop']
    ); ?>

    <?php
    if(array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
        echo $form->field($model, 'estado')->dropDownList(
            ['Active' => 'Active', 'Inactive' => 'Inactive']
        );

        echo $form->field($model, 'fecha_cancelado')->widget(
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
        ]);
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
