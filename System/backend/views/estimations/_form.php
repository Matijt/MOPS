<?php

use backend\models\Estimations;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use backend\models\Order;
use backend\models\Stocklist;

/* @var $this yii\web\View */
/* @var $model backend\models\Estimations */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="estimations-form">

    <?php

    if (!$model->isNewRecord) {
        $model->fecha = date('d-m-Y', strtotime($model->fecha));
    }else{
        $model->fecha = date('d-m-Y');
    }
    $form = ActiveForm::begin();
    $id = $model->order_idorder;

    if($id) {
        $order = Order::findOne($id);
        $pastEstimations = Estimations::find()
            ->joinWith('orderIdorder')
            ->andFilterWhere(['=', 'order.Hybrid_idHybrid', $order->Hybrid_idHybrid])->all();

        $countPastEstimations1 = Estimations::find()
            ->joinWith('orderIdorder')
            ->andFilterWhere(['=', 'order.Hybrid_idHybrid', $order->Hybrid_idHybrid])
            ->andFilterWhere(['!=', 'gramPerFruit', 0])
            ->count();

        $countPastEstimations2 = Estimations::find()
            ->joinWith('orderIdorder')
            ->andFilterWhere(['=', 'order.Hybrid_idHybrid', $order->Hybrid_idHybrid])
            ->andFilterWhere(['!=', 'gramPerFruit2', 0])
            ->count();

        if ($countPastEstimations1 == 0){
            $countPastEstimations1 = 1;
        }
        if ($countPastEstimations2 == 0){
            $countPastEstimations2 = 1;
        }

        if ($pastEstimations != null) {
            $GPF1 = 0;
            $GPF2 = 0;
            foreach ($pastEstimations AS $pastEstimation){
                $GPF1 = $GPF1 + $pastEstimation->gramPerFruit;
                $GPF2 = $GPF2 + $pastEstimation->gramPerFruit2;
            }
            $GPF1 = $GPF1/$countPastEstimations1;
            $GPF2 = $GPF2/$countPastEstimations2;
        }
    }else{
        $GPF1 = "Select an order.";
        $GPF2 = "Select an order.";
    }
    ?>

    <div class="row">

        <div class="col-lg-12">
            <?=
            $form->field($model, 'order_idorder')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Order::find()->joinWith('compartmentIdCompartment')->joinWith('hybridIdHybr')->orderBy('idorder')->andFilterWhere(['=', 'order.delete',0])                        ->andFilterWhere(['=', 'order.state', 'Active'])
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
                            
                            ($("#estimations-instock").val(res[3]));
                            ($("#estimations-fruitsharvest").val(res[4]));
                            
                            
                           }else{
                            ($("#NT").attr("hidden", false));
                            ($("#T").attr("hidden", true)); 
                            ($("#registry-fruitscount").val(res[1]));
                            
                            document.getElementById("gf1").innerHTML = "<option value=\'"+res[3]+"\'>Avarage</option>";
                            document.getElementById("gf2").innerHTML = "<option value=\'"+res[4]+"\'>Avarage</option>";
                            
                           
                                                       
                           }
                            ($("#n").html("plantas totales: "+res[2]));
                           });
                           ',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?></div>
        <!--        <div class="col-lg-4">
            <?= $form->field($model, 'totalFemalesCount')->textInput() ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'totalPlantsCheked')->textInput() ?>
        </div>-->
    </div>
    <hr class="btn-success">
    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'fecha')->widget(
                \dosamigos\datepicker\DatePicker::className(), [
                // inline too, not bad
                //        'inline' => true,
                'language' => 'en_EN',
                'value' => 'dd-mm-yyyy',
                // modify template for custom rendering
                //        'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ],
                'options' => [
                    'placeholder' => Yii::t('app', 'Choose Order'),
                    'onchange' => '
                           $.post("index.php?r=registry/istomato2&id="' . '+($("#estimations-order_idorder").val()+"&date="'.'+$(this).val()), function( data ){
                           var res = data.split(",");
                            ($("#estimations-instock").val(res[0]));
                            ($("#estimations-fruitsharvest").val(res[1]));
                           
                           });
                           ',
                ],
            ]); ?>
        </div>
    </div>
    <hr class="btn-info">
    <?php
    //    if(!$model->tsw || !$model->fruitsExtracted || !$model->seedsExtracted) {
    ?>
    <!--    <div id="exp" hidden="true">
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'inStock')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'fruitsHarvest')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'plantsTotal')->textInput() ?>
            </div>
        </div>
-->
    <?php
    //  }else{
    if ($model->isNewRecord){
        $model->fecha = date('d-m-Y');
        $model->factorLess = 10;
        ?>

        <div class="row" id="T" >
            <div class="col-lg-4">
                <?= $form->field($model, 'inStock')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'fruitsHarvest')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, "factorLess")->input('number'); ?>
            </div>
        </div>

        <div class="row" id="NT" hidden>
            <div class="col-lg-12">
                <?= $form->field($model, "gramPerFruit")->textInput(['type' => 'numeric', 'list' => 'gf1']); ?>
            </div>
        </div>
    <?php
    }else{
        $model->fecha = date('d-m-Y', strtotime($model->fecha));

        ?>
            <div class="row" id="T">
                <div class="col-lg-4">
                    <?= $form->field($model, 'inStock')->textInput() ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'fruitsHarvest')->textInput() ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, "factorLess")->input('number'); ?>
                </div>
            </div>

            <div class="row" id="NT" hidden>
                <div class="col-lg-6">
                    <?= $form->field($model, "gramPerFruit")->textInput(['type' => 'numeric', 'list' => 'gf1']); ?>
                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, "gramsSet1")->input('number'); ?>
                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, "gramPerFruit2")->textInput(['type' => 'numeric', 'list' => 'gf2']); ?>
                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, "gramsSet2")->input('number'); ?>
                </div>

            </div>
        <?php

        if(substr($model->orderIdorder->hybridIdHybr->variety, 0, 1) == "T") {
            echo "<script>
                    ($(\"#NT\").attr(\"hidden\", true));
                    ($(\"#T\").attr(\"hidden\", false)); 
                </script>";
        }else{
            echo "<script>
                    ($(\"#NT\").attr(\"hidden\", false));
                    ($(\"#T\").attr(\"hidden\", true)); 
                </script>";
        }
    }
    ?>


    <?php
    //  }
    ?>



    <datalist id ="gf1">
        <?php
        echo "<option value='".$GPF1."'>Avarage</option>";
        ?>
    </datalist>
    <datalist id ="gf2">
        <?php
            echo "<option value='".$GPF2."'>Avarage</option>";
        ?>
    </datalist>
<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

</div>
