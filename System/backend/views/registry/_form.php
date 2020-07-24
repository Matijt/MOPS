<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model backend\models\Registry */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="registry-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']);
    if ($model->isNewRecord) {
        ?>

        <div class="row">
<!--
Permite el filtrar las ordenes por compartimento, pero se necesita que se seleccione una orden así que es mejor quitarlo.
            <div class="col-sm-6">
                <?= $form->field($model, 'numRow')->dropDownList(
                    \yii\helpers\ArrayHelper::map(\backend\models\Compartment::find()->all(), 'idCompartment', 'compNum'),
                    [
                        'prompt' => 'Select Compartments',
                        'onchange' => '
                           $.post("index.php?r=registry/orders&comp="' . '+($(this).val()), function( data ){
                           $("#registry-order_idorder").html(data);
                           })
                           '
                    ]
                )->label('Compartment') ?>
            </div>
            -->
            <div class="col-sm-12">
                <?=
                $form->field($model, 'order_idorder')->widget(\kartik\select2\Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map(\backend\models\Order::find()->joinWith('compartmentIdCompartment')->joinWith('hybridIdHybr')->orderBy('idorder')->andFilterWhere(['=', 'order.delete', 0])
                        ->andFilterWhere(['=', 'order.state', 'Active'])
                        ->andFilterWhere(['=', 'order.trial_id', 1])->all(),
                        'idorder', 'fullName'),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Choose Order'),
                        'onchange' => '
                           $.post("index.php?r=registry/istomato&id="' . '+($(this).val()), function( data ){
                           var res = data.split(",");
                           if(res[0] == "T"){
                            ($("#T").attr("hidden", false));
                            ($("#NT").attr("hidden", true));
                            ($("#registry-fruitscount").val("0"));
                           }else{
                            ($("#NT").attr("hidden", false));
                            ($("#T").attr("hidden", true)); 
                            ($("#registry-fruitscount").val(res[1]));
                           }
                            ($("#n").html("plantas totales: "+res[2]));
                           });
                           ',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
        </div>

        <hr class='btn-primary'>

        <div class="row" id="NT" hidden>
            <div class="col-sm-6">
                <?= $form->field($model, "fruitsCount")->textInput(['type' => 'number', 'readonly' => true]); ?>
                <b>
                    <p id="n"></p>
                </b>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, "quantity")->textInput(['type' => 'number']); ?>
            </div>
        </div>
        <div class="row" id="T">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Add Registry</h4></div>
                <div class="panel-body">
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items', // required: css class selector
                        'widgetItem' => '.item', // required: css class
                        'limit' => 20, // the maximum times, an element can be cloned (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item', // css class
                        'deleteButton' => '.remove-item', // css class
                        'model' => $quantities[0],
                        'formId' => 'dynamic-form',
                        'formFields' => [
                            'quantity',
                        ],
                    ]); ?>

                    <div class="container-items"><!-- widgetContainer -->
                        <?php foreach ($quantities as $i => $quantity): ?>
                            <div class="item panel panel-default col-sm-4"><!-- widgetBody -->
                                <div class="panel-heading">
                                    <div class="pull-right">
                                        <button type="button" class="add-item btn btn-success btn-xs"><i
                                                    class="glyphicon glyphicon-plus"></i></button>
                                        <button type="button" class="remove-item btn btn-danger btn-xs"><i
                                                    class="glyphicon glyphicon-minus"></i></button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    // necessary for update action.
                                    if (!$quantity->isNewRecord) {
                                        echo Html::activeHiddenInput($quantity, "[{$i}]idregistry");
                                    }
                                    ?>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <?= $form->field($quantity, "[{$i}]numRow")->textInput(['type' => 'number']); ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <?= $form->field($quantity, "[{$i}]quantity")->textInput(['type' => 'number']); ?>
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
    }else {
        ?>

        <div class="row">
            <div class="col-sm-3">
                <?=
                $form->field($model, 'order_idorder')->widget(\kartik\select2\Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map(\backend\models\Order::find()->joinWith('compartmentIdCompartment')->joinWith('hybridIdHybr')->orderBy('idorder')
                        ->andFilterWhere(['=', 'order.delete', 0])
                        ->andFilterWhere(['=', 'order.state', 'Active'])
                        ->andFilterWhere(['=', 'order.trial_id', 1])->all(),
                        'idorder', 'fullName'),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Choose Order'),
                        'onchange' => '
                           $.post("index.php?r=registry/istomato&id="' . '+($(this).val()), function( data ){
                           var res = data.split(",");
                           if(res[0] == "T"){
                            ($("#start").attr("class", "col-lg-6"));
                            ($("#nr").append($("#start")));
                            ($("#end").attr("hidden", false));
                            ($("#nr").attr("hidden", false));
                            ($("#fc").attr("hidden", true));
                            ($("#registry-fruitscount").val("0"));
                           }else{
                            ($("#start").attr("class", "col-lg-4"));
                            ($("#fc").append($("#start")));
                            ($("#fc").append($("#end")));
                            ($("#end").attr("hidden", false));
                            ($("#fc").attr("hidden", false));
                            ($("#nr").attr("hidden", true)); 
                            ($("#registry-fruitscount").val(res[1]));
                           }
                            ($("#n").html(res[2]));
                           });
                           ',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>


			<?php
            if(substr($model->orderIdorder->hybridIdHybr->variety, 0, 1) == "T"){
            ?>

                <div class="col-sm-9" id="fc" hidden>
                    <div class="col-sm-4">
                        <?= $form->field($model, "fruitsCount")->textInput(['type' => 'number', 'readonly' => true]); ?>
                    </div>
                </div>
                <div class="col-sm-9" id="nr">
                    <div class="col-sm-6">
                        <?= $form->field($model, "numRow")->input('numeric'); ?>
                    </div>

			<?php
            }else{
            ?>

                    <div class="col-sm-9" id="nr" hidden>
                        <div class="col-sm-6">
                            <?= $form->field($model, "numRow")->input('numeric'); ?>
                        </div>
                    </div>

                <div class="col-sm-9" id="fc" >
                    <div class="col-sm-4">
                        <?= $form->field($model, "fruitsCount")->textInput(['type' => 'number', 'readonly' => true]); ?>
                    </div>

			<?php
            }
            ?>


            <?php
                    if(substr($model->orderIdorder->hybridIdHybr->variety, 0, 1) == "T"){
                    ?>

                    <div class="col-sm-6" id="start">
                        <?= $form->field($model, "quantity")->input('numeric'); ?>
                    </div>

                        <?php
                        }else{
                            ?>

                        <div class="col-sm-4" id="start">
                            <?= $form->field($model, "quantity")->input('numeric'); ?>
                        </div>

                            <?php
                        }
                        ?>


            <?php
            if(substr($model->orderIdorder->hybridIdHybr->variety, 0, 1) == "T"){
            ?>

            <div class="col-sm-4" id="end" hidden>
                <?= $form->field($model, "quantity2")->input('numeric'); ?>
            </div>
        </div>

        <?php
        }else{
            ?>

                    <div class="col-sm-4" id="end">
                        <?= $form->field($model, "quantity2")->input('numeric'); ?>
                    </div>
                </div>

            <?php
        }
        ?>

            <!--

			<?php
			if(substr($model->orderIdorder->hybridIdHybr->variety, 0, 1) == "T"){

			?>
            <div class="col-sm-9" id="fc" hidden>
                <div class="col-sm-4">
                    <?= $form->field($model, "fruitsCount")->textInput(['type' => 'number', 'readonly' => true]); ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, "quantity")->input('numeric'); ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, "quantity2")->input('numeric'); ?>
                </div>
                <b>
                    <p id="n"></p>
                </b>
            </div>
                <div class="col-sm-9" id="nr">
                    <div class="col-sm-6">
                        <?= $form->field($model, "numRow")->input('numeric'); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, "quantity")->input('numeric'); ?>
                    </div>
                </div>
			<?php
			}else{
			?>

                <div class="col-sm-8" id="fc" hidden>
                    <div class="col-sm-6">
                        <?= $form->field($model, "numRow")->input('numeric'); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, "quantity")->input('numeric'); ?>
                    </div>
                </div>
                <div class="col-sm-12">

                </div>
            <div class="col-sm-9" id="nr">
                <div class="col-sm-4">
                    <?= $form->field($model, "fruitsCount")->textInput(['type' => 'number', 'readonly' => true]); ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, "quantity")->input('numeric'); ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, "quantity2")->input('numeric'); ?>
                </div>
                <b>
                    <p id="n"><?php
                        echo "Plantas totales: ";
                        if ($model->orderIdorder->realisedNrOfPlantsF > 0){
                            echo $model->orderIdorder->realisedNrOfPlantsF;
                        }else{
                            echo $model->orderIdorder->netNumOfPlantsF;
                        }
                        ;?></p>
                </b>

                <b>sadjsakldjksaljdkl</b>
            </div>
			<?php
			}
			?>
			-->
        </div>
        <?php
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
