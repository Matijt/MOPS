<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\Compartment;
use backend\models\Nursery;
use backend\models\Hybrid;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use kartik\widgets\Select2;


/* @var $this yii\web\View */
/* @var $model backend\models\Order */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="order-form">

    <?php $form = ActiveForm::begin();

    $model->ReqDeliveryDate = date('d-m-Y', strtotime($model->ReqDeliveryDate));
    $model->orderDate = date('d-m-Y', strtotime($model->orderDate));
    $model->ssRecDate = date('d-m-Y', strtotime($model->ssRecDate));
    $model->sowingDateM = date('d-m-Y', strtotime($model->sowingDateM));
    $model->transplantingM = date('d-m-Y', strtotime($model->transplantingM));
    $model->pollenColectF = date('d-m-Y', strtotime($model->pollenColectF));
    $model->pollenColectU = date('d-m-Y', strtotime($model->pollenColectU));
    $model->steamDesinfectionF = date('d-m-Y', strtotime($model->steamDesinfectionF));
    $model->steamDesinfectionU = date('d-m-Y', strtotime($model->steamDesinfectionU));
    ?>



    <?php
    if(isset($error)){
        if($error = 1){
            echo "<script>alert('You can´t use "." rows in the compartment "."')</script>";
        }
    }
    ?>
    <div class="row">
        <div class="col-lg-4">
            <?php
            if($model->isNewRecord) {
                $model->ReqDeliveryDate = date('d-m-Y');
                $model->orderDate = date('d-m-Y');
                $model->ssRecDate = date('d-m-Y');
                $model->sowingDateM = date('d-m-Y');
            }
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
            ?>
            <hr class="btn-warning">
            <div class="row">
                <?php
                if(!$model->isNewRecord) {
                    echo "<div class='col-lg-3'>";
                    echo    $form->field($model, 'numCrop')->textInput()."<br>";
                    echo "</div>";
                    echo '<div class="col-lg-3">';
                    echo $form->field($model, 'contractNumber')->textInput();
                    echo "</div>";

                    echo '<div class="col-lg-3">';
                    echo  $form->field($model, 'Hybrid_idHybrid')->widget(Select2::className(), [
                            'data' => ArrayHelper::map(
                                Hybrid::find()->joinWith(['fatherIdFather', 'motherIdMother', 'cropIdcrops'])->andFilterWhere(['=', '`hybrid`.`delete`', '0'])
                                    ->andFilterWhere(['=', '`father`.`delete`', '0'])
                                    ->andFilterWhere(['=', '`crop`.`delete`', '0'])
                                    ->andFilterWhere(['=', '`mother`.`delete`', '0'])->all(),
                                'idHybrid', 'fatherIdFather.variety'),
                            'options' =>
                                ['prompt' => 'Select father',
                                    'onchange' => '
                                
                                $.post("index.php?r=order/history&id=' . '"+$(this).val(), function( data ){
                                    $("input#gp").attr("selectBoxOptions", data);                         
                                    createEditableSelect(document.forms[1].gpOrder);
                                });
                                
                                $.post("index.php?r=order/historyf&id=' . '"+$(this).val(), function( data ){
                                    $("input#gpof").attr("selectBoxOptions", data);                         
                                    createEditableSelect(document.forms[1].germinationPOF);
                                });
                                
                                ',
                                ]
                        ]
                    )->label('Father');
                    echo "</div>";
                    echo '<div class="col-lg-2">';
                    echo $form->field($model, 'numRowsOpt')->textInput();
                    echo "</div>";



                }else{



                    echo '<div class="col-lg-4">';
                    echo $form->field($model, 'contractNumber')->textInput();
                    echo "</div>";
                    echo '<div class="col-lg-4">';
                    echo  $form->field($model, 'Hybrid_idHybrid')->widget(Select2::className(), [
                            'data' => ArrayHelper::map(
                                Hybrid::find()->joinWith(['fatherIdFather', 'motherIdMother', 'cropIdcrops'])->andFilterWhere(['=', '`hybrid`.`delete`', '0'])
                                    ->andFilterWhere(['=', '`father`.`delete`', '0'])
                                    ->andFilterWhere(['=', '`crop`.`delete`', '0'])
                                    ->andFilterWhere(['=', '`mother`.`delete`', '0'])->all(),
                                'idHybrid', 'fatherIdFather.variety'),
                            'options' =>
                                ['prompt' => 'Select father',
                                    'onchange' => '

                                $.post("index.php?r=order/history&id=' . '"+$(this).val(), function( data ){
                                    var gpdll=document.getElementById("gpdl");
                                    gpdll.innerHTML = data;
                                });
                                
                                $.post("index.php?r=order/historyf&id=' . '"+$(this).val(), function( data ){
                                    var gpdll=document.getElementById("gpofdl");
                                    gpdll.innerHTML = data;
                                });'
                                    ,
                                ]
                        ]
                    )->label('Father');
                    echo "</div>";
                    echo '<div class="col-lg-4">';
                    echo $form->field($model, 'numRowsOpt')->textInput([
                        'onchange' =>'
                                 $.post("index.php?r=order/compartment&gp=1&nump=1&numpf=1&kg=1&rows="'.'+$(this).val()+"&males=1", function( data ){
                                  $("#order-compartment_idcompartment").html(data);
                                });
                            '
                    ]);
                    echo "</div>";
                }
                ?>

            </div>
            <hr class="btn-info">
            <div class="row">
                <div class="col-lg-6">
                    <?=
                    $form->field($model, 'germinationPOM')->textInput(['id' => 'gpom', 'name' => 'germinationPOM',
                        'type' => 'textarea','list' => 'gpomdl',
                    ]);
                    ?>
                </div>
                <div class="col-lg-6">

                    <?= $form->field($model, 'compartment_idCompartment')->dropDownList(
                        ArrayHelper::map(Compartment::find()->all(), 'idCompartment', 'compNum'),
                        [
                            'prompt' => 'Select Compartments',
                            'onchange' =>'
                    $.post("index.php?r=order/setdate&date="'.'+($(this).val
                    ()), 
                    function( data ){
                        $("#order-sowingdatem").val(data);
                     });
                       '
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
                <?php

                if(($model->isNewRecord)){
                    echo '<div class="col-lg-2"></div>';
                    echo '<div class="col-lg-4">';
                }else{
                    echo '<div class="col-lg-3"></div>';
                    echo '<div class="col-lg-6">';
                }

                echo $form->field($model, 'state')->dropDownList([
                    "Active" => "Active",
                    "Canceled" => "Canceled"],
                    ['prompt' => 'Seleccione estado']);
                echo "</div>";

                if(($model->isNewRecord)){
                    echo '<div class="col-lg-4">';
                    echo $form->field($model, 'sowingDateM')->widget(
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
                    echo "</div>";
                }



                ?>
                <div class="col-lg-2"></div>
            </div>
            <hr class="btn-success">

            <?php
            if(!($model->isNewRecord)){
                echo "<div class='row'><div class='col-lg-6'>";
                if(strtotime(date('d-m-Y', strtotime($model->sowingDateM))) >  strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'sowingDateM')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                } else{
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
                echo "</div><div class ='col-lg-6'>";


                if(strtotime(date('d-m-Y', strtotime($model->transplantingM))) >  strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'transplantingM')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                }else{
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

                echo "</div></div>";
                echo "<hr class='btn-info'>";
                echo "<div class='row'>";
                echo "<div class='col-lg-6'>";

                if(strtotime(date('d-m-Y', strtotime($model->pollenColectF))) >  strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'pollenColectF')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                }else{
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

                echo "</div>";
                echo "<div class='col-lg-6'>";

                if(strtotime(date('d-m-Y', strtotime($model->pollenColectU))) >  strtotime(date('d-m-Y'))) {
                    echo $form->field($model, 'pollenColectU')->widget(
                        DatePicker::className(), [
                        'language' => 'en_EN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ],
                        'options' => ['class' => 'ui-datepicker2']
                    ]);
                }else{
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
                echo "</div></div>";
                echo "<hr class='btn-success'>";
                echo "<div class='row'>";
                echo "<div class='col-lg-6'>";

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
                echo "<div class='col-lg-6'>";

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
                echo "<hr class='btn-warning'>";
                echo "<div class='row'>";
                echo "<div class='col-lg-6'>";
                echo $form->field($model, 'realisedNrOfPlantsM')->textInput();
                echo "</div>";
                echo "<div class='col-lg-6'>";
                echo $form->field($model, 'extractedPlantsM')->textInput();
                echo "</div></div>";
                echo "<hr class='btn-primary'>";
            }
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