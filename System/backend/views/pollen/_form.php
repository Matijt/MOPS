<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\Order;
use kartik\widgets\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\backend\models\Pollen */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="pollen-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']);

    if($model->harvestDate) {
        $model->harvestDate = date('d-m-Y', strtotime($model->harvestDate));
    }
    if ($model->useWeek) {
        $model->useWeek = date('d-m-Y', strtotime($model->useWeek));
    }
    ?>
    <?php
    if ($model->isNewRecord){
        ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Create Pollen </h4></div>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 20, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelspollen[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'harvestWeek',
                        'harvestDate',
                        'harvestMl',
                        'useWeek',
                        'useMl',
                        'order_idorder',
                    ],
                ]); ?>

                <div class="container-items"><!-- widgetContainer -->
                    <?php foreach ($modelspollen as $i => $modelpollen): ?>
                        <div class="item panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Pollen</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (! $modelpollen->isNewRecord) {
                                    echo Html::activeHiddenInput($modelpollen, "[{$i}]id");
                                }
                                ?>
                                <?= $form->field($modelpollen, "[{$i}]harvestWeek")->textInput(['maxlength' => true]) ?>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?= $form->field($modelpollen, "[{$i}]harvestDate")->widget(\yii\jui\DatePicker::classname(), [
                                            //'language' => 'ru',
                                            'dateFormat' => "dd-M-yyyy",
                                            'options' => ['class' => 'form-control picker']
                                        ]) ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($modelpollen, "[{$i}]harvestMl")->textInput(['maxlength' => true, 'placeholder' => 'HarvestMl']) ?>
                                    </div>
                                </div><!-- .row -->
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= $form->field($modelpollen, "[{$i}]useWeek")->widget(\yii\jui\DatePicker::classname(), [
                                            //'language' => 'ru',
                                            'dateFormat' => "dd-M-yyyy",
                                            'options' => ['class' => 'form-control picker']
                                        ]) ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= $form->field($modelpollen, "[{$i}]useMl")->textInput(['maxlength' => true, 'placeholder' => 'UseMl']) ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= $form->field($modelpollen, "[{$i}]order_idorder")->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Order::find()->joinWith('compartmentIdCompartment')->joinWith('hybridIdHybr')->orderBy('idorder')->all(),
            'idorder', 'fullName'),
        'options' => ['placeholder' => Yii::t('app', 'Choose Order')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>
                                    </div>
                                </div><!-- .row -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>


        <?php

    }else{

    ?>
    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'harvestWeek')->textInput(['placeholder' => 'HarvestWeek']) ?>

    <?= $form->field($model, 'harvestDate')->widget(
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
    ]);?>

    <?= $form->field($model, 'harvestMl')->textInput(['maxlength' => true, 'placeholder' => 'HarvestMl']) ?>

    <?= $form->field($model, 'useWeek')->widget(
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
    ]);?>

    <?= $form->field($model, 'useMl')->textInput(['maxlength' => true, 'placeholder' => 'UseMl']) ?>

    <?= $form->field($model, 'order_idorder')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Order::find()->joinWith('compartmentIdCompartment')->joinWith('hybridIdHybr')->orderBy('idorder')->all(),
            'idorder', 'fullName'),
        'options' => ['placeholder' => Yii::t('app', 'Choose Order')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>


    <?php
    }
    ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end()?>

</div>
