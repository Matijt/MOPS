<!-- Initialize the plugin: -->

<?php

use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use backend\models\Order;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use backend\models\Hybrid;
use yii\data\SqlDataProvider;
use backend\models\OrderSearch;
use backend\controllers\OrderController;
use yii\helpers\Html;
use dosamigos\multiselect\MultiSelect;
use kartik\export\ExportMenu;

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
/*
    function showInfo(){
        if(($("#columns").val())){
            $.post("index.php?r=order/orderhistory&columns="'.'+($("#columns").val())+"&id="'.'+$(this).val(), function( data ){
                $("#1").html(data);
            });
        }
    }
*/
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
    ?>
    <div class="row">
        <h1>Select attributes orders</h1>
        <div class='col-sm-3'>

            <label class="control-label" for="hibrids">Select Hybrids</label>
            <br>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#hybrids').multiselect({
                    enableClickableOptGroups: true,
                    enableCollapsibleOptGroups: true,
                    enableFiltering: true,
                    maxHeight: 300
                });
            });
        </script>

        <?php
        /* echo $form->field($model, 'Hybrid_idHybrid')->widget(MultiSelect::classname(), [
                'data' => ArrayHelper::map(Hybrid::find()->all(), 'idHybrid', 'variety'),
                "options" => ['multiple'=>"multiple"]
            ]
        );

        echo $form->field($model, 'Hybrid_idHybrid')->widget(\dosamigos\multiselect\MultiSelectListBox::classname(), [
                'data' => ArrayHelper::map(Hybrid::find()->all(), 'idHybrid', 'variety'),
                "options" => ['multiple'=>"multiple"]
            ]
        );*/
        ?>

            <!-- Build your select: -->

            <select id="hybrids" multiple="multiple" name="hybrids">
                <?php
                $hibrids = Hybrid::find()->andFilterWhere(['=', 'delete' , 0])->all();
                foreach ($hibrids AS $hibrid){
                    echo '<option v alue="'.$hibrid->idHybrid.'">'.$hibrid->variety.'</option>';
                }
                ?>
            </select>
    </div>
                <div class="col-sm-3">
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
                        <option value="o.numCrop" selected="selected"># Crop</option>
                        <option value="cr.crop AS Crop" selected="selected">Crop</option>
                        <option value="c.compNum AS Compartment" selected="selected">Number of compartment</option>
                        <option value="h.variety AS Hybrid" selected="selected">Hybrid Variety</option>
                        <option value="m.variety AS Mother" selected="selected">Mother Variety</option>
                        <option value="f.variety AS Father" selected="selected">Father Variety</option>
                        <option value="o.orderKg" selected="selected">Order (Kg)</option>
                        <option value="o.gpOrder AS GramsPerPlantOrder" selected="selected">Grams per Plant Order</option>
                        <option value="(SELECT ROUND(AVG(ord.gpOrder), 2) FROM `order` ord WHERE ord.Hybrid_idHybrid = h.idHybrid) AS GramsPerPlantAverage" selected="selected">Grams per Plant average</option>
                        <option value="o.numRows" selected="selected">Number of Rows</option>
                        <option value="o.calculatedYield">Calculated Yield</option>
                        <option value="o.netNumOfPlantsF">Net Number Of Plants Females</option>
                        <option value="o.netNumOfPlantsM">Net Number Of Plants Male</option>
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
                        <option value="DATE_FORMAT(o.sowingDateM, '%d/%m/%Y') AS SowingDateMmale">Sowing Date Male</option>
                        <option value="DATE_FORMAT(o.sowingDateF, '%d/%m/%Y') AS SowingDateFemale">Sowing Date Female</option>
                        <option value="DATE_FORMAT(o.transplantingM, '%d/%m/%Y') AS TransplantingMale">Transplanting Male</option>
                        <option value="DATE_FORMAT(o.transplantingF, '%d/%m/%Y') AS TransplantingFemale">Transplanting Female</option>
                        <option value="DATE_FORMAT(o.pollenColectF, '%d/%m/%Y') AS PollenCollectF">Pollen Harvest From</option>
                        <option value="DATE_FORMAT(o.pollenColectU, '%d/%m/%Y') AS PollenCollectU">Pollen Harvest Until</option>
                        <!--<option value="o.pollenColectQ">Pollen Colect Quantity</option>-->
                        <option value="DATE_FORMAT(o.pollinationF, '%d/%m/%Y') AS PollinationF">Pollination From</option>
                        <option value="DATE_FORMAT(o.pollinationU, '%d/%m/%Y') AS PollinationU">Pollination Until</option>
                        <option value="DATE_FORMAT(o.harvestF, '%d/%m/%Y') AS HarvestF">Harvest From</option>
                        <option value="DATE_FORMAT(o.harvestU, '%d/%m/%Y') AS HarvestU">Harvest Until</option>
                        <option value="DATE_FORMAT(o.steamDesinfectionF, '%d/%m/%Y') AS SteamDesinfectionF">Steam Desinfection From</option>
                        <option value="DATE_FORMAT(o.steamDesinfectionU, '%d/%m/%Y') AS SteamDesinfectionU">Steam Desinfection Until</option>
                        <option value="o.remarks">Order remarks</option>
                        <option value="c.rowsNum AS `Rows Num Comp`">Rows of the compartment</option>
                        <option value="c.grossSurface">Gross Surface</option>
                        <option value="c.netSurface">Net Surface</option>
                        <option value="c.grossLength">Gross Length</option>
                        <option value="c.netLength">Net Length</option>
                        <option value="c.width">Width</option>
                        <option value="m.steril AS SterileMother">Mother Sterile</option>
                        <option value="m.germination AS Germination_Holland">Germination Holland</option>
                        <option value="m.tsw AS Thousand_seed_weight_Mother">Mother Thousand seed weight</option>
                        <option value="m.gP AS Grams_per_Plant_From_Holland">Grams Per plant From Holland</option>
                        <option value="f.steril AS SterileFather">Father Sterile</option>
                        <option value="f.germination AS Germination_Father">Father Germination</option>
                        <option value="f.tsw AS Thousand_seed_weight_Father">Father Thousand seed weight</option>
                        <optgroup label = "Stocklist">
                        <option value="stho.totalNumberOfFruitsHarvested AS TotalNumberOfFruitsHarvested">Total Number Of Fruits Harvested</option>
                        <option value="stho.totalWetSeedWeight AS TotalWetSeedWeight">Total Wet Seed weight</option>
                        <option value="stho.totalDrySeedWeight AS TotalDrySeedWeight">Total Dry Seed Weight</option>
                        <option value="stho.totalAvarageWeightOfSeedsPF  AS TotalAverageWeightOfSeedsPerFruit">Total Average Weight Of Seeds Per Fruit</option>
                        <option value="stho.totalNumberOfBags AS TotalNumberOfBags">Total Number Of Bags</option>
                        <option value="stho.totalInStock AS TotalInStock">Total In Stock</option>
                        <option value="stho.totalShipped AS TotalShipped">Total Shipped</option>
                        <option value="stho.avarageGP AS AverageGramsPerPlantStocklist">Average Grams Per Plant Stocklist</option>
                        <option value="stho.phase AS Phase">Phase</option>
                        <option value="stho.lotNr AS LotNumber">Lot Number</option>

                        <!--                    <option value="n.numcompartment AS Nursery">Nursery</option>
                                            <option value="n.tablesFloors">Tables Floors</option>
                                            <option value="n.quantity">Quantity</option>-->
                    </select>

<!--                                        <input type="button" id="select_all_columnas" name="select_all_crops" value="Select All Columns">
                                        <input type="button" id="deselect_all_columnas" name="deselect_all_crops" value="Deselect All Columns">
                                        <script>
                                            $('#select_all_columnas').click(function() {
                                                $('#columns option').prop('selected', true);
                                            });
                                            $('#deselect_all_columnas').click(function() {
                                                $('#columns option').prop('selected', false);
                                            });
                                        </script>-->

                </div>
                <div class="col-sm-3">
                    <label class="control-label" for="crops">Select Crop Numbers</label>
                    <br>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $('#crops').multiselect({
                                enableClickableOptGroups: true,
                                enableCollapsibleOptGroups: true,
                                enableFiltering: true,
                                maxHeight: 300
                            });
                        });
                    </script>
                    <!-- Build your select: -->
                    <select id="crops" multiple="multiple" name="crops">
                        <?php
                        $max = Order::find()->orderBy('numCrop DESC')->limit(1)->one()->numCrop;
                        $crops = \backend\models\Numcrop::find()->andFilterWhere(['<=', 'cropnum' , $max])->all();
                        foreach ($crops AS $crop){
                            echo '<option value="'.$crop->cropnum.'" selected="selected">'.$crop->cropnum.'</option>';
                        }
                        ?>
                    </select>

<!--                    <input type="button" id="select_all_crops" name="select_all_crops" value="Select All Crops">
                    <input type="button" id="deselect_all_crops" name="deselect_all_crops" value="Deselect All Crops">
                    <script>
                        $('#select_all_crops').click(function() {
                            $('#crops option').prop('selected', true);
                        });
                        $('#deselect_all_crops').click(function() {
                            $('#crops option').prop('selected', false);
                        });
                    </script>-->
                </div>
                <div class="col-sm-3">
                    <label class="control-label" for="crops">Select Compartments</label>
                    <br>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $('#compartments').multiselect({
                                enableClickableOptGroups: true,
                                enableCollapsibleOptGroups: true,
                                enableFiltering: true,
                                maxHeight: 300
                            });
                        });
                    </script>
                    <!-- Build your select: -->
                    <select id="compartments" multiple="multiple" name="compartments">
                        <?php
                        $compartments = \backend\models\Compartment::find()->all();
                        foreach ($compartments AS $compartment){
                            echo '<option value="'.$compartment->idCompartment.'" selected="selected">'.$compartment->compNum.'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <div class="row">
                <div class='col-sm-12'>
                    <?php
                    echo Html::button('Filter', ['id'=>"dialog", 'class' => 'btn btn-info btn-block',
                        'onclick' =>'
                        if (Object.keys(($("#hybrids").val())).length < 1) {
                            alert("You most select at least one hibrid.");
                        }else{
                            window.open(
                            "index.php?r=order/orderhistory&columns="'.'+($("#columns").val())+"&id="'.'+($("#hybrids").val())+"&crops="'.'+($("#crops").val())+"&compartments="'.'+($("#compartments").val())
                            );
                        }
                        ',
                    ]);
                    ?>


                </div>
            </div>
<div class="row">
    <div class="col-sm-12">
    <div id="1">

            </div>
        </div>
    </div>
</div>

