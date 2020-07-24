<!-- Initialize the plugin: -->

<?php

use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use backend\models\Order;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use backend\models\Hybrid;
use yii\grid\GridView;
use yii\data\SqlDataProvider;
use backend\models\OrderSearch;
use backend\controllers\OrderController;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
    var expanded = false;

    function showCheckboxes() {
        var checkboxes = document.getElementById("checkboxes");
        if (!expanded) {
            checkboxes.style.display = "block";
            expanded = true;
        } else {
            checkboxes.style.display = "none";
            expanded = false;
        }
    }
</script>
<style>
    .multiselect {
        width: 100%;
    }

    .selectBox {
        position: relative;
    }

    .selectBox select {
        width: 100%;
        font-weight: bold;
    }

    .overSelect {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
    }

    #checkboxes {
        display: none;
        border: 1px #dadada solid;
    }

    #checkboxes label {
        display: inline-block;
        margin: 2px;
        padding: 1px 5px;
    }

    #checkboxes label:hover {
        background-color: #1e90ff;
    }
</style>
<div class="order-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $form = ActiveForm::begin();
    $model = new Order();
    echo "<h1>Select attributes orders</h1>";
    echo "<div class='col-sm-6'>".$form->field($model, 'Hybrid_idHybrid')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Hybrid::find()->all(), 'idHybrid', 'variety'),
            'options' =>
                ['prompt' => 'Select hybrid',

                    'onchange' =>'
                                               if(($("#columns").val())){
                        $.post("index.php?r=order/orderhistory&columns="'.'+($("#columns").val())+"&id="'.'+$(this).val(), function( data ){
                            $("#1").html(data);
                        });
}
                    ',
                ]
        ]
    );

    ?>
</div>
<div class="row">
    <div class="col-sm-6">
        <label class="control-label" for="columns">Select Columns</label>
        <br>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#columns').multiselect({
                    enableClickableOptGroups: true,
                    enableCollapsibleOptGroups: true,
                    enableFiltering: true,
                    maxHeight: 300
                });
            });
        </script>
        <!-- Build your select: -->
        <select id="columns" multiple="multiple" name="columnas">
            <optgroup label="Compartment">
            <option value="c.compNum AS Compartment" selected="selected">Number of the compartment</option>
            <option value="c.rowsNum AS `Rows Num Comp`">Rows of the compartment</option>
            <option value="c.grossSurface">Gross Surface</option>
            <option value="c.netSurface">Net Surface</option>
            <option value="c.grossLength">Gross Length</option>
            <option value="c.netLength">Net Length</option>
            <option value="c.width">Width</option>
            </optgroup>
            <optgroup label="Hybrid">
                <option value="h.variety AS Hybrid" selected="selected">Variety</option>
            </optgroup>
            <optgroup label="Mother">
                <option value="m.variety AS Mother" selected="selected">Variety</option>
                <option value="m.steril AS SterilMother">Steril</option>
                <option value="m.germination AS Germination_Mother">Germination</option>
                <option value="m.tsw AS Thousand_seed_weight_Mother">Thousand seed weight</option>
                <option value="m.gP">GP</option>
            </optgroup>
            <optgroup label="Father">
                <option value="f.variety AS Father" selected="selected">Variety</option>
                <option value="f.steril AS Steril_Father">Steril</option>
                <option value="f.germination AS Germination_Father">Germination</option>
                <option value="f.pollenProduction">Pollen Production From Holland</option>
                <option value="f.tsw AS Thousand_seed_weight_Father">Thousand seed weight</option>
            </optgroup>
            <optgroup label="Crop">
                <option value="cr.crop AS Crop" selected="selected">Crop</option>
            </optgroup>
            <optgroup label="Nursery">
                <option value="n.numcompartment AS Nursery" selected="selected">Nursery</option>
                <option value="n.tablesFloors">Tables Floors</option>
                <option value="n.quantity">Quantity</option>
            </optgroup>
            <optgroup label="Order">
                <option value="o.numCrop" selected="selected">Num Crop</option>
                <option value="o.orderKg" selected="selected">Order (Kg)</option>
                <option value="o.numRows" selected="selected">Number of Rows</option>
                <option value="o.calculatedYield">Calculated Yield</option>
                <option value="o.netNumOfPlantsF">Net Number Of Plants Females</option>
                <option value="o.ReqDeliveryDate">Requested Delivery Date</option>
                <option value="o.contractNumber">Contract Number</option>
                <option value="o.ssRecDate">Stock Seed Recieved Date</option>
                <option value="o.sowingM">Sowing Male Number</option>
                <option value="o.sowingF">Sowing Female Number</option>
                <option value="o.nurseryM">Nursery Male</option>
                <option value="o.nurseryF">Nursery Female</option>
                <option value="o.realisedNrOfPlantsM">Realised Number Of Plants Male</option>
                <option value="o.realisedNrOfPlantsF">Realised Number Of Plants Female</option>
                <option value="o.remainingPlantsM">Remaining Plants Male</option>
                <option value="o.remainingPlantsF">Remaining Plants Female</option>
                <option value="o.sowingDateM">Sowing Date Male</option>
                <option value="o.sowingDateF">Sowing Date Female</option>
                <option value="o.transplantingM">Transplanting Male</option>
                <option value="o.transplantingF">Transplanting Female</option>
                <option value="o.pollenColectF">PollenColect From</option>
                <option value="o.pollenColectU">Pollen Colect Until</option>
                <option value="o.pollenColectQ">Pollen Colect Quantity</option>
                <option value="o.pollinationF">Pollination From</option>
                <option value="o.pollinationU">Pollination Until</option>
                <option value="o.harvestF">Harvest From</option>
                <option value="o.harvestU">Harvest Until</option>
                <option value="o.steamDesinfectionF">Steam Desinfection From</option>
                <option value="o.steamDesinfectionU">Steam Desinfection Until</option>
                <option value="o.remarks">Order remarks</option>
            </optgroup>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
    <?php Pjax::begin(); ?>
    <div id="1">

            </div>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
