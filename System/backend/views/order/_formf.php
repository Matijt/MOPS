<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\Compartment;
use backend\models\Hybrid;
use dosamigos\datepicker\DatePicker;
use kartik\widgets\Select2;


/* @var $this yii\web\View */
/* @var $model backend\models\Order */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="order-form">

    <?php $form = ActiveForm::begin();
    $facil = new \backend\codigo\Facil();
    $model->FMRatio = $facil->limitarDecimales($model->FMRatio);
    $model->Density = $facil->limitarDecimales($model->Density);

    $model->ReqDeliveryDate = date('d-m-Y', strtotime($model->ReqDeliveryDate));
    $model->orderDate = date('d-m-Y', strtotime($model->orderDate));
    $model->ssRecDate = date('d-m-Y', strtotime($model->ssRecDate));
    $model->sowingDateM = date('d-m-Y', strtotime($model->sowingDateM));
    $model->sowingDateF = date('d-m-Y', strtotime($model->sowingDateF));
    $model->transplantingM = date('d-m-Y', strtotime($model->transplantingM));
    $model->transplantingF = date('d-m-Y', strtotime($model->transplantingF));
    $model->pollenColectF = date('d-m-Y', strtotime($model->pollenColectF));
    $model->pollenColectU = date('d-m-Y', strtotime($model->pollenColectU));
    $model->pollinationF = date('d-m-Y', strtotime($model->pollinationF));
    $model->pollinationU = date('d-m-Y', strtotime($model->pollinationU));
    $model->harvestF = date('d-m-Y', strtotime($model->harvestF));
    $model->harvestU = date('d-m-Y', strtotime($model->harvestU));
    $model->steamDesinfectionF = date('d-m-Y', strtotime($model->steamDesinfectionF));
    $model->steamDesinfectionU = date('d-m-Y', strtotime($model->steamDesinfectionU));
    ?>




    <div class="row">
            <?php
            if($model->isNewRecord) {
                $model->NumOfPlantsPerRow = 75;
                $model->NumOfFPRow = 60;
                $model->NumOfMPRow = 12;
                $model->FMRatio = 4;
                $model->plantingDistance = 50;

                $model->ReqDeliveryDate = date('d-m-Y');
                $model->orderDate = date('d-m-Y');
                $model->ssRecDate = date('d-m-Y');
                $model->sowingDateM = date('d-m-Y');
                $model->sowingDateF = date('d-m-Y');
                $model->transplantingM = date('d-m-Y');
                $model->transplantingF = date('d-m-Y');
                $model->pollenColectF = date('d-m-Y');
                $model->pollenColectU = date('d-m-Y');
                $model->pollinationF = date('d-m-Y');
                $model->pollinationU = date('d-m-Y');
                $model->harvestF = date('d-m-Y');
                $model->harvestU = date('d-m-Y');
                $model->steamDesinfectionF = date('d-m-Y');
                $model->steamDesinfectionU = date('d-m-Y');

                echo '<div class="col-lg-3">';
                echo "<br>" . $form->field($model, 'ReqDeliveryDate')->widget(
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
            echo "</div><div class='col-lg-3'>";
            echo "<br>" . $form->field($model, 'orderDate')->widget(
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

            echo "</div><div class='col-lg-3'>";
            echo "<br>" . $form->field($model, 'ssRecDate')->widget(
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

            echo "</div><div class='col-lg-3'><br><br>";
                echo $form->field($model, 'prueba')->checkbox(array(
                    'label'=>'Not use Father',
                    'onchange' => ' 
                    $("#male").each(function() {
                        if ($(this).is(":hidden")) {
                            // handle non visible state for date picker of male

                            $(this).show();
                            $("#female").hide();
                        } else {
                            // handle visible state for date picker of male
                            $(this).hide();
                            $("#female").show();
                        }
                        
                    });                 
                    '
                ));
            echo "</div></div>";
            }else{
                echo '<div class="col-lg-4">';
                echo "<br>" . $form->field($model, 'ReqDeliveryDate')->widget(
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
                echo "</div><div class='col-lg-4'>";
                echo "<br>" . $form->field($model, 'orderDate')->widget(
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

                echo "</div><div class='col-lg-4'>";
                echo "<br>" . $form->field($model, 'ssRecDate')->widget(
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

                echo "</div></div>";
            }
            ?>
            <hr class="btn-warning">
            <div class="row">
                <?php
                    echo "<div class='col-lg-2'>";
                    echo    $form->field($model, 'numCrop')->textInput(['type' => 'number'])."<br>";
                    echo "</div>";
                    echo "<div class='col-lg-2'>";
                    echo $form->field($model, 'orderKg')->textInput(['type' => 'number']);
                    echo "</div>";
                    echo '<div class="col-lg-3">';
                    echo $form->field($model, 'contractNumber')->textInput(['type' => 'number']);
                    echo "</div>";

                    echo '<div class="col-lg-3">';
                    echo  $form->field($model, 'Hybrid_idHybrid')->widget(Select2::className(), [
                            'data' => ArrayHelper::map(
                                Hybrid::find()->joinWith(['fatherIdFather', 'motherIdMother', 'cropIdcrops'])->andFilterWhere(['=', '`hybrid`.`delete`', '0'])
                                    ->andFilterWhere(['=', '`father`.`delete`', '0'])
                                    ->andFilterWhere(['=', '`crop`.`delete`', '0'])
                                    ->andFilterWhere(['=', '`mother`.`delete`', '0'])->all(),
                                'idHybrid', 'variety'),
                        ]
                    );
                    echo "</div>";
                    echo '<div class="col-lg-2">';
                    echo $form->field($model, 'numRowsOpt')->textInput(['type' => 'number']);
                    echo "</div>";

                ?>
            </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'NumOfPlantsPerRow')->textInput(['type' => 'number']);?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'NumOfFPRow')->textInput(['type' => 'number']);?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'NumOfMPRow')->textInput(['type' => 'number']);?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'calculatedYield')->textInput(['type' => 'number']);?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'FMRatio')->textInput(['type' => 'number']);?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'Density')->textInput(['type' => 'number']);?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'plantingDistance')->textInput(['type' => 'number']);?>
            </div>
        </div>
            <hr class="btn-info">
            <div class="row">
                <div class="col-lg-2">
                    <?=
                    $form->field($model, 'germinationPOF')->textInput([
                        'type' => 'textarea',
                        'id' => 'gpof',
                        'name' => 'germinationPOF','list' => 'gpofdl',
                    ]);
                    ?>
                </div>
                <div class="col-lg-2">
                    <?=
                    $form->field($model, 'germinationPOM')->textInput(['id' => 'gpom', 'name' => 'germinationPOM',
                        'type' => 'number','list' => 'gpomdl',
                    ]);
                    ?>
                </div>

                <div class="col-lg-2">
                    <?php
                        echo $form->field($model, 'gpOrder')->textInput(
                            [
                                'type' => 'number',
                                'id' =>'gp',
                                'name' => 'gpOrder',
                                'list' => 'gpdl',
                            ]);
                    ?>
                </div>
                <div class="col-lg-6">

                    <?= $form->field($model, 'compartment_idCompartment')->widget(Select2::className(), [
                            'data' => ArrayHelper::map(Compartment::find()->all(), 'idCompartment', 'compNum'),
//                            'prompt' => 'Select Compartments',
                        ]
                    ) ?>
                </div>
                <?php
                /*echo $form->field($model, 'nursery_idnursery')->dropDownList(
                    ArrayHelper::map(Nursery::find()->all(), 'idnursery', 'numcompartment'),
                    [
                        'prompt' => 'Select Nursery',
    //                    'onchange' => '$.get( "'.Url::toRoute('/order/validcompartment').'", { id:$(this).val(); try:$(this).val() }, function(data) {
      //                  $("select#models-contact").html(data);
        //                });'
                    ]
                ) */?>
            </div>
            <div class="row">
                    <div class="col-lg-4"></div>
                    <div class="col-lg-4">
                        <?=
                        $form->field($model, 'state')->dropDownList([
                            "Active" => "Active",
                            "Canceled" => "Canceled"],
                            ['prompr' => 'Seleccione estado']);
                        ?>
                    </div>
                    <div class="col-lg-4"></div>
            </div>

<?php
        echo "<hr class='btn-warning'>";
        echo "<div class='row'>";

            echo "<div class='col-lg-3'>";
            echo $form->field($model, 'netNumOfPlantsM')->textInput(['type' => 'number']);
            echo "</div>";

            echo "<div class='col-lg-3'>";
            echo $form->field($model, 'nurseryM')->textInput(['type' => 'number']);
            echo "</div>";

            echo "<div class='col-lg-3'>";
            echo $form->field($model, 'realisedNrOfPlantsM')->textInput(['type' => 'number']);
            echo "</div>";

            echo "<div class='col-lg-3'>";
            echo $form->field($model, 'extractedPlantsM')->textInput(['type' => 'number']);
            echo "</div>";

            echo "<div class='col-lg-3'>";
            echo $form->field($model, 'netNumOfPlantsF')->textInput(['type' => 'number']);
            echo "</div>";

            echo "<div class='col-lg-3'>";
            echo $form->field($model, 'nurseryF')->textInput(['type' => 'number']);
            echo "</div>";

            echo "<div class='col-lg-3'>";
            echo $form->field($model, 'realisedNrOfPlantsF')->textInput(['type' => 'number']);
            echo "</div>";

            echo "<div class='col-lg-3'>";
            echo $form->field($model, 'extractedPlantsF')->textInput(['type' => 'number']);
        echo "</div></div>";
        ?>
            <hr class="btn-success">


            <?php
                echo "<div class='row'><div class='col-lg-3'>";
                if (strtotime(date('d-m-Y', strtotime($model->sowingDateM))) > strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'sowingDateM')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                } else {
                    echo $form->field($model, 'sowingDateM')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }

                echo "</div><div class ='col-lg-3'>";
                if (strtotime(date('d-m-Y', strtotime($model->sowingDateF))) > strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'sowingDateF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                } else {
                    echo $form->field($model, 'sowingDateF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }

                echo "</div><div class ='col-lg-3'>";

                if (strtotime(date('d-m-Y', strtotime($model->transplantingM))) > strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'transplantingM')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                } else {
                    echo $form->field($model, 'transplantingM')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }

                echo "</div><div class ='col-lg-3'>";
                if (strtotime(date('d-m-Y', strtotime($model->transplantingF))) > strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'transplantingF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                } else {
                    echo $form->field($model, 'transplantingF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }

                echo "</div><div class ='col-lg-3'>";

                if (strtotime(date('d-m-Y', strtotime($model->pollenColectF))) > strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'pollenColectF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                } else {
                    echo $form->field($model, 'pollenColectF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }

                echo "</div><div class ='col-lg-3'>";

                if (strtotime(date('d-m-Y', strtotime($model->pollenColectU))) > strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'pollenColectU')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                } else {
                    echo $form->field($model, 'pollenColectU')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }

                echo "</div><div class ='col-lg-3'>";

                if (strtotime(date('d-m-Y', strtotime($model->pollenColectF))) > strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'pollinationF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                } else {
                    echo $form->field($model, 'pollinationF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }

                echo "</div><div class ='col-lg-3'>";
                if (strtotime(date('d-m-Y', strtotime($model->pollinationU))) > strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'pollinationU')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                } else {
                    echo $form->field($model, 'pollinationU')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }
                echo "</div></div>";




                echo "<hr class='btn-success'>";
                echo "<div class='row'>";
                echo "<div class='col-lg-3'>";

                if(strtotime(date('d-m-Y', strtotime($model->harvestF))) >  strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'harvestF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                }else{
                    echo $form->field($model, 'harvestF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }

                echo "</div>";
                echo "<div class='col-lg-3'>";


                if(strtotime(date('d-m-Y', strtotime($model->harvestU))) >  strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'harvestU')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                }else{
                    echo $form->field($model, 'harvestU')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }

                echo "</div>";
                echo "<div class='col-lg-3'>";

                if(strtotime(date('d-m-Y', strtotime($model->steamDesinfectionF))) >  strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'steamDesinfectionF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                }else{
                    echo $form->field($model, 'steamDesinfectionF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }

                echo "</div>";
                echo "<div class='col-lg-3'>";

                if(strtotime(date('d-m-Y', strtotime($model->steamDesinfectionU))) >  strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'steamDesinfectionU')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                }else{
                    echo $form->field($model, 'steamDesinfectionU')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker']
                    ]);
                }

                echo "</div></div>";
                echo "<hr class='btn-primary'>";
            ?>

            <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

            <div class="form-group">
                <?php
                if(isset($name)) {
                    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                }else{
                    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                }
                ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
        <datalist id ="gpdl">
        </datalist>
        <datalist id ="gpomdl">
        </datalist>
        <datalist id ="gpofdl">
        </datalist>
        <script type="text/javascript">
            //            createEditableSelect(document.forms[1].myText2);
        </script>


        <style type="text/css">
            .ui-datepicker {
                background: #b3e6b3;
                border: 1px solid #555;
            }
            .ui-datepicker2 {
                color: red;
                border: 1px solid #555;
            }
        </style>