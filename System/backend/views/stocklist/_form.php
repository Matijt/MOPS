<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\Compartment;
use backend\models\Nursery;
use backend\models\Hybrid;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\Stocklist */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stocklist-form">

    <?php $form = ActiveForm::begin();

    if (!$model->isNewRecord){
        if ($model->harvestDate){
            $model->harvestDate = date('m/d/Y', strtotime($model->harvestDate));
        }
        if ($model->cleaningDate){
            $model->cleaningDate = date('m/d/Y', strtotime($model->cleaningDate));
        }
        if ($model->shipmentDate){
            $model->shipmentDate = date('m/d/Y', strtotime($model->shipmentDate));
        }
    }
    ?>


    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'harvestNumber')->textInput() ?>
        </div>
        <div class="col-lg-6">
            <?=
            $form->field($model, 'harvestDate')->widget(
                DatePicker::className(), [
                // inline too, not bad
//        'inline' => true,
                'language' => 'en_EN',
                'value' => 'dd-mm-yyyy',
                // modify template for custom rendering
//        'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                'options' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'numberOfFruitsHarvested')->textInput() ?>
        </div>
        <div class="col-lg-6">
            <?=
            $form->field($model, 'cleaningDate', ['template' => "{label} {input} <span class='status'>&nbsp;</span> {error}"])->widget(
                DatePicker::className(), [
                // inline too, not bad
//        'inline' => true,
                'language' => 'ea_EN',
                'value' => 'dd-mm-yyyy',
                // modify template for custom rendering
//        'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                'options' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                ],
            ]);
            ?>
        </div>
    </div>
    <hr class="btn-info">

<script>
    $('#stocklist-shipmentdate').change(function(){
        if($(this).val().length !== 0){
            ($("#see").attr("hidden", false));
            ($("#ship").attr("class", "col-lg-4"));
        }else{
            ($("#see").attr("hidden", true));
            ($("#ship").attr("class", "col-lg-12"));
        }
    });
</script>


    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'wetSeedWeight')->textInput() ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'drySeedWeight')->textInput() ?>
        </div>
    </div>

    <hr class="btn-warning">

    <div class="row">
        <?php
        if(!$model->isNewRecord && $model->shipmentDate){
        ?>
        <div class="col-lg-4" id="ship">
            <?php
            }else{
            ?>
            <div class="col-lg-12" id="ship">

            <?php
            }
            ?>
            <?=
            $form->field($model, 'shipmentDate')->widget(
                DatePicker::className(), [
                // inline too, not bad
//        'inline' => true,
                'language' => 'en_EN',
                'value' => 'dd-mm-yyyy',
                // modify template for custom rendering
//        'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                'options' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]);
            ?>
        </div>




    <?php
    if(!$model->isNewRecord && $model->shipmentDate){
?>
        <div id="see">
  <?php  }else{
        ?>
      <div id="see" hidden>
    <?php
    }
    ?>

            <div class="col-lg-4">
                <?=$form->field($model, 'numberOfBags')->textInput();?>
            </div>
            <div class="col-lg-4">
                <?=$form->field($model, 'cartonNo')->textInput();?>
            </div>
            <div class="col-lg-6">
                <?=$form->field($model, 'packingListDescription')->textInput(['maxlength' => true]);?>
            </div>
            <div class="col-lg-6">
                <?=$form->field($model, 'moisture')->textInput();?>
            </div>
        </div>
    </div>

    <hr class="btn-success">

            <div class="row">
                <div class="col-lg-4">
                    <?= $form->field($model, 'wap')->textInput(['type' => 'number']) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'fruitColor')->textInput() ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'ringColor')->textInput() ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <?= $form->field($model, 'destroyed')->dropDownList(
                        ['' => 'No Destroyed', 'Destroyed' => 'Destroyed' ]
                    )
                    ?></div>
                <div class="col-lg-6">
                    <?= $form->field($model, 'tsw')->textInput() ?>
                </div>
            </div>

    <hr class="btn-danger">

    <?= $form->field($model, 'remarksSeeds')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
