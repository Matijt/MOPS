<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\Registrynursery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="registrynursery-form">

    <?php $form = ActiveForm::begin();

    if($model->isNewRecord){
        $model->recDate = date('d-m-Y');
        $model->sowing = date('d-m-Y');
        $model->transplant = date('d-m-Y');
        $model->trasplantCompartment = date('d-m-Y');
    }
    $model->recDate = date('d-m-Y', strtotime($model->recDate));
    $model->sowing = date('d-m-Y', strtotime($model->sowing));
    $model->transplant = date('d-m-Y', strtotime($model->transplant));
    $model->trasplantCompartment = date('d-m-Y', strtotime($model->trasplantCompartment));
    ?>

    <div class="row">
        <div class="col-sm-6">
            <?=
            $form->field($model, 'order_idorder')->widget(\kartik\select2\Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Order::find()->joinWith('compartmentIdCompartment')->joinWith('hybridIdHybr')->orderBy('idorder')->andFilterWhere(['=', 'order.delete', 0])
                    ->andFilterWhere(['>', 'order.steamDesinfectionU', date('Y-m-d')])
                    ->andFilterWhere(['=', 'order.state', 'Active'])
                    ->andFilterWhere(['!=', 'order.sowingDateF', '1970-01-01'])
                    ->andFilterWhere(['=', 'order.trial_id', 1])->all(),
                    'idorder', 'fullName'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Choose Order'),
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'FM')->dropDownList(
                    ['F' => 'Female', 'M' => 'Male']
            ) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'batch')->textInput() ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'prodLot')->textInput() ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'shipment')->textInput(['type' => 'number']) ?>
        </div>
    </div>
    <hr class="btn-warning">
    <div class="row">
        <div class="col-lg-4">
            <?=$form->field($model, 'recDate')->widget(
                DatePicker::className(), [
                'language' => 'en_EN',
                'value' => 'dd-mm-yyyy',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]);?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'seedsRecieved')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'realSeedsRecieved')->textInput(['type' => 'number']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'numRows')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'numPlants')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'estimatedGermination')->textInput(['type' => 'number', 'step' => '0.01']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'usedGermination')->textInput(['type' => 'number', 'step' => '0.01']) ?>
        </div>
    </div>
    <hr class="btn-success">
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'nursery_idnursery')->dropDownList(
                \yii\helpers\ArrayHelper::map(\backend\models\Nursery::find()->all(), 'idnursery', 'numcompartment'),
                [
                    'prompt' => 'Select Compartment',
                ]
            ) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'sowedTable')->textInput() ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'seedsReallyGerminated')->textInput(['type' => 'number']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'nursery_idnursery1')->dropDownList(
                \yii\helpers\ArrayHelper::map(\backend\models\Nursery::find()->all(), 'idnursery', 'numcompartment'),
                [
                    'prompt' => 'Select Compartment',
                ]
            ) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'TidelFloor')->textInput() ?>
        </div>
    </div>
    <hr class="btn-info">
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'figure_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(\backend\models\Figure::find()->all(), 'id', 'figure'),
                [
                    'prompt' => 'Select Figure',
                ]
            ) ?>
        </div>
        <div class="col-lg-6">
            <?=$form->field($model, 'trasplantCompartment')->widget(
                DatePicker::className(), [
                'language' => 'en_EN',
                'value' => 'dd-mm-yyyy',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]);?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
