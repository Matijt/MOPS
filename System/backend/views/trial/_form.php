<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use \yii\jui\DatePicker;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use backend\models\Order;
use backend\models\Hybrid;

/* @var $this yii\web\View */
/* @var $model backend\models\Trial */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trial-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <?php
    if ($model->isNewRecord) {
        ?>
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'numRows')->Input('number', [
                    'min' => '1',
                    'onchange' => '$.post("index.php?r=order/compartment&gp=1&nump=1&numpf=1&kg=1&rows="' . '+($(this).val())+"&males=0", function( data ){
                                   $("#trial-compartment_idcompartment").html(data);
                               })'
                ]); ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'compartment_idCompartment')->dropDownList(
                    \yii\helpers\ArrayHelper::map(\backend\models\Compartment::find()->all(), 'idCompartment', 'compNum'),
                    [
                        'prompt' => 'Select Compartments',
                    ]
                ) ?>
            </div>
        </div>
        <?php
    }else {
        ?>
        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'numRows')->Input('number', [
                    'min' => '1',
                ]); ?>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'observations')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Create Orders </h4></div>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 20, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelsorder[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'ReqDeliveryDate',
                        'orderDate',
                        'ssRecDate',
                        'Hybrid_idHybrid',
                        'germinationPOF',
                        'germinationPOM',
                        'sowingDateM'
                    ],
                ]); ?>
                <div class="container-items"><!-- widgetContainer -->
                    <?php foreach ($modelsorder as $i => $modelorder): ?>
                        <div class="item panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Order</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (! $modelorder->isNewRecord) {
                                    echo Html::activeHiddenInput($modelorder, "[{$i}]idorder");
                                }
                                ?>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= $form->field($modelorder, "[{$i}]ReqDeliveryDate")->widget(\yii\jui\DatePicker::classname(), [
                                            //'language' => 'ru',
                                            'dateFormat' => "dd-M-yyyy",
                                            'options' => ['class' => 'form-control picker']
                                        ]) ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= $form->field($modelorder, "[{$i}]orderDate")->widget(\yii\jui\DatePicker::classname(), [
                                            //'language' => 'ru',
                                            'dateFormat' => "dd-M-yyyy",
                                            'options' => ['class' => 'form-control picker']
                                        ]) ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= $form->field($modelorder, "[{$i}]ssRecDate")->widget(\yii\jui\DatePicker::classname(), [
                                            //'language' => 'ru',
                                            'dateFormat' => "dd-M-yyyy",
                                            'options' => ['class' => 'form-control picker']
                                        ]) ?>
                                    </div>
                                </div><!-- .row -->


                                <div class="row">
                                    <div class="col-sm-4">
                                        <?= $form->field($modelorder, "[{$i}]Hybrid_idHybrid")->widget(Select2::classname(), [
                                            'data' => ArrayHelper::map(Hybrid::find()->andFilterWhere(['=', 'delete', 0])->all(),
                                                'idHybrid', 'variety'),
                                            'options' => ['placeholder' => Yii::t('app', 'Choose Hybrid')],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],
                                        ]); ?>
                                    </div>
                                    <div class="col-sm-2">
                                        <?= $form->field($modelorder, "[{$i}]germinationPOF")->Input('number', [
                                            'min' => '1',
                                        ]);?>
                                    </div>
                                    <div class="col-sm-2">
                                        <?= $form->field($modelorder, "[{$i}]germinationPOM")->Input('number', [
                                            'min' => '1',
                                        ]);?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?php
                                        if ($modelorder->isNewRecord){
                                            echo $form->field($modelorder, "[{$i}]sowingDateM")->widget(\yii\jui\DatePicker::classname(), [
                                                //'language' => 'ru',
                                                'dateFormat' => "dd-M-yyyy",
                                                'options' => ['class' => 'form-control picker']
                                            ]);
                                        }else {
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->sowingDateM))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]sowingDateM")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]sowingDateM")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            }
                                        }
                                        ?>
                                    </div>
                                </div><!-- .row -->

                                <?php
                                // necessary for update action.
                                if (! $modelorder->isNewRecord) {
                                    ?>

                                    <hr class='btn-success'>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <?php
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->sowingDateF))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]sowingDateF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]sowingDateF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            } ?>
                                        </div>
                                        <div class="col-sm-4">
                                            <?php
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->transplantingM))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]transplantingM")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]transplantingM")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            } ?>
                                        </div>
                                        <div class="col-sm-4">
                                            <?php
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->transplantingF))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]transplantingF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]transplantingF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            } ?>
                                        </div>
                                    </div><!-- .row -->
                                    <hr class='btn-info'>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <?php
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->pollenColectF))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]pollenColectF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]pollenColectF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            } ?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?php
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->pollenColectU))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]pollenColectU")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]pollenColectU")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            } ?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?php
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->pollinationF))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]pollinationF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]pollinationF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            } ?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?php
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->pollinationU))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]pollinationU")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]pollinationU")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            } ?>
                                        </div>
                                    </div><!-- .row -->
                                    <hr class='btn-warning'>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <?php
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->harvestF))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]harvestF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]harvestF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            } ?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?php
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->harvestU))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]harvestU")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]harvestU")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            } ?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?php
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->steamDesinfectionF))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]steamDesinfectionF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]steamDesinfectionF")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            } ?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?php
                                            if (strtotime(date('d-m-Y', strtotime($modelorder->steamDesinfectionU))) > strtotime(date('d-m-Y'))) {
                                                echo $form->field($modelorder, "[{$i}]steamDesinfectionU")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    color: red;']
                                                ]);
                                            } else {
                                                echo $form->field($modelorder, "[{$i}]steamDesinfectionU")->widget(\yii\jui\DatePicker::classname(), [
                                                    //'language' => 'ru',
                                                    'dateFormat' => "dd-M-yyyy",
                                                    'options' => ['class' => 'form-control picker', 'style' => '    background-color: rgb(179, 230, 179);']
                                                ]);
                                            } ?>
                                        </div>
                                    </div><!-- .row -->
                                    <hr class='btn-danger'>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <?= $form->field($modelorder, "[{$i}]realisedNrOfPlantsM")->Input('number', [
                                                'min' => '1',
                                            ]);?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?= $form->field($modelorder, "[{$i}]extractedPlantsM")->Input('number', [
                                                'min' => '1',
                                            ]);?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?= $form->field($modelorder, "[{$i}]realisedNrOfPlantsF")->Input('number', [
                                                'min' => '1',
                                            ]);?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?= $form->field($modelorder, "[{$i}]extractedPlantsF")->Input('number', [
                                                'min' => '1',
                                            ]);?>
                                        </div>
                                    </div><!-- .row -->
                                    <hr class='btn-default'>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?= $form->field($modelorder, "[{$i}]remarks")->textarea(['rows' => 4]) ?>
                                        </div>
                                    </div><!-- .row -->
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <datalist id ="gpdl">
    </datalist>
    <datalist id ="gpomdl">
    </datalist>
    <datalist id ="gpofdl">
    </datalist>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
