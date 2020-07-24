<?php

use backend\models\StocklistHasOrder;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Order;
use backend\models\Stocklist;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\StocklistHasOrder */
/* @var $modelSL backend\models\Stocklist*/
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stocklist-has-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-2">
            <?=
            $form->field($model, 'phase')->input('numeric');
            ?>
        </div>
        <div class="col-lg-2">
            <?=
            $form->field($model, 'lotNr')->input('numeric');
            ?>
        </div>
        <div class="col-lg-8">
            <?=
            $form->field($model, 'order_idorder')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Order::find()->joinWith('compartmentIdCompartment')->joinWith('hybridIdHybr')->andFilterWhere(['=', 'order.delete', 0])->orderBy(['numCrop' => SORT_DESC,  'compartment_idCompartment' => SORT_DESC])->all(),
                    'idorder', 'fullName'),
                'options' => ['placeholder' => Yii::t('app', 'Choose Order')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Order');
            ?>
        </div>
    </div>
    <?php
    if ($model->isNewRecord) {
        ?>

        <hr class="btn-success">

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($modelSL, 'harvestNumber')->textInput() ?>
            </div>
            <div class="col-lg-6">
                <?=
                $form->field($modelSL, 'harvestDate')->widget(
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
                <?= $form->field($modelSL, 'numberOfFruitsHarvested')->textInput() ?>
            </div>
            <div class="col-lg-6">
                <?=
                $form->field($modelSL, 'cleaningDate', ['template' => "{label} {input} <span class='status'>&nbsp;</span> {error}"])->widget(
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
            $('#stocklist-shipmentdate').change(function () {
                if ($(this).val().length !== 0) {
                    ($("#see").attr("hidden", false));
                    ($("#ship").attr("class", "col-lg-4"));
                } else {
                    ($("#see").attr("hidden", true));
                    ($("#ship").attr("class", "col-lg-12"));
                }
            });
        </script>


        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($modelSL, 'wetSeedWeight')->textInput() ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($modelSL, 'drySeedWeight')->textInput() ?>
            </div>
        </div>
        <?php
    }
    ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

</div>
