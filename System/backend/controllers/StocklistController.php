<?php

namespace backend\controllers;

use backend\models\Compartment;
use backend\models\Father;
use backend\models\Hybrid;
use backend\models\Mother;
use backend\models\NumcropHasCompartment;
use backend\models\Order;
use backend\models\Pollen;
use backend\models\StocklistHasOrder;
use backend\models\StocklistHasOrderSearch;
use Yii;
use backend\models\Stocklist;
use backend\models\StocklistSearch;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii2mod\rbac\filters\AccessControl;

/**
 * StocklistController implements the CRUD actions for Stocklist model.
 */
class StocklistController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['create', 'update', 'delete', 'import-excel', 'import-excel2', 'import-excel3', 'import-excel4', 'import-excel5', 'import-excel6', 'import-excel7', 'import-excel8', 'import-excel9', 'import-excel10', 'import-excel11', 'import-excel12', 'import-excel13', 'import-excel14'],
                        'allow' => true,
                        'roles' => ['Administrator', 'Estimator', 'Estimator Helper'],
                    ],
                    [
                        'actions' => ['index', 'view',
                        ],
                        'allow' => true,
                        'roles' => ['Viewer', 'Administrator', 'Holland', 'Estimator', 'Estimator Helper'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Stocklist models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StocklistSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $models = new Pollen();
        if ($models->load(Yii::$app->request->post())) {
            echo "hola";
            $models->file = UploadedFile::getInstance($models,'file');
            if ($models->file){
                $models->file->saveAs('uploads/stocklist.xlsx');
                return $this->redirect('index.php?r=stocklist%2Fimport-excel');
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $models
        ]);
    }

    /**
     * Displays a single Stocklist model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {


        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Stocklist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($order)
    {
        $model = new Stocklist();

        if ($model->load(Yii::$app->request->post())) {

            if($model->harvestDate) {
                $model->harvestDate = date('Y-m-d', strtotime($model->harvestDate));
            }
            if ($model->shipmentDate ) {
                $model->shipmentDate = date('Y-m-d', strtotime($model->shipmentDate));
            }
            if($model->cleaningDate ) {
                $model->cleaningDate = date('Y-m-d', strtotime($model->cleaningDate));
            }
            if($model->drySeedWeight && $model->numberOfFruitsHarvested) {
                $model->avgWeightOfSeedPF = ($model->drySeedWeight / $model->numberOfFruitsHarvested);
            }
            if ($model->packingListDescription == 'Last harvest'){
                $model->eol = "Yes";
            }else{
                $model->eol = "No";
            }
            if($model->drySeedWeight){
                $model->status = "In Stock";
            }
            if($model->shipmentDate){
                $model->status = "Shipped";
            }
            if($model->destroyed == "Destroyed"){
                $model->status = "Destroyed";
            }
            if(!$model->status){
                $model->status = "Incomplete";
            }

            $modelSOs = StocklistHasOrderSearch::find()->andFilterWhere(['=' ,'order_idorder',$order])->one();
            $orderA = Order::findOne($order);

            if ($model->numberOfFruitsHarvested){
                $modelSOs->totalNumberOfFruitsHarvested = $modelSOs->totalNumberOfFruitsHarvested+$model->numberOfFruitsHarvested;
            }
            if($model->wetSeedWeight){
                $modelSOs->totalWetSeedWeight = $modelSOs->totalWetSeedWeight+$model->wetSeedWeight;
            }
            if ($model->drySeedWeight){
                $modelSOs->totalDrySeedWeight = $modelSOs->totalDrySeedWeight+$model->drySeedWeight;
            }
            $modelSOs->totalAvarageWeightOfSeedsPF = ($modelSOs->totalDrySeedWeight/$modelSOs->totalNumberOfFruitsHarvested);
            if ($model->numberOfBags){
                $modelSOs->totalNumberOfBags = $modelSOs->totalNumberOfBags+$model->numberOfBags;
            }
            if($model->status == "In Stock"){
                $modelSOs->totalInStock = $modelSOs->totalInStock+$model->drySeedWeight;
            }else if($model->status == "Shipped"){
                $modelSOs->totalShipped = $modelSOs->totalShipped+$model->drySeedWeight;
            }
            if($orderA->remainingPlantsF) {
                $modelSOs->avarageGP = $modelSOs->totalDrySeedWeight / $orderA->remainingPlantsF;
            }else if($orderA->remainingPlantsF){
                $modelSOs->avarageGP = $modelSOs->totalDrySeedWeight / $orderA->remainingPlantsF;
            }else{
                $modelSOs->avarageGP = $modelSOs->totalDrySeedWeight / $orderA->nurseryF;
            }
            if($modelSOs->save()){

                $model->hasOrderId = $modelSOs->order_idorder;
                $model->LUser = Yii::$app->user->identity->username;
                $model->save();

                echo "<script>window.history.back();</script>";
            }else{
                echo "<script>window.history.back();</script>";
            }
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Stocklist model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelOld = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if($model->harvestDate) {
                $model->harvestDate = date('Y-m-d', strtotime($model->harvestDate));
            }
            if ($model->shipmentDate ) {
                $model->shipmentDate = date('Y-m-d', strtotime($model->shipmentDate));
            }
            if($model->cleaningDate ) {
                $model->cleaningDate = date('Y-m-d', strtotime($model->cleaningDate));
            }
            if($model->drySeedWeight && $model->numberOfFruitsHarvested) {
                $model->avgWeightOfSeedPF = ($model->drySeedWeight / $model->numberOfFruitsHarvested);
            }
            if ($model->packingListDescription == 'Last harvest'){
                $model->eol = "Yes";
            }else{
                $model->eol = "No";
            }
            if($model->drySeedWeight){
                $model->status = "In Stock";
            }
            if($model->shipmentDate){
                $model->status = "Shipped";
            }
            if($model->destroyed == "Destroyed"){
                $model->status = "Destroyed";
            }
            if(!$model->status){
                $model->status = "Incomplete";
            }
            $model->LUser = Yii::$app->user->identity->username;
            if ($model->save()) {
                $hasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $model->hasOrderId])->one();
                if($hasOrder){
                $hasOrder->totalNumberOfFruitsHarvested = $hasOrder->totalNumberOfFruitsHarvested - $modelOld->numberOfFruitsHarvested + $model->numberOfFruitsHarvested;

                $hasOrder->totalWetSeedWeight = $hasOrder->totalWetSeedWeight - $modelOld->wetSeedWeight + $model->wetSeedWeight;
                $hasOrder->totalDrySeedWeight = $hasOrder->totalDrySeedWeight - $modelOld->drySeedWeight+ $model->drySeedWeight;
                $hasOrder->totalAvarageWeightOfSeedsPF = ($hasOrder->totalDrySeedWeight/$hasOrder->totalNumberOfFruitsHarvested);
                if($modelOld->numberOfBags){
                    $hasOrder->totalNumberOfBags = $hasOrder->totalNumberOfBags - $modelOld->numberOfBags;
                }
                if($model->numberOfBags){
                    $hasOrder->totalNumberOfBags = $hasOrder->totalNumberOfBags + $model->numberOfBags;
                }

                if($model->status == "In Stock"){
                    $hasOrder->totalInStock = $hasOrder->totalInStock + $model->drySeedWeight;
                }else if($model->status == "Shipped"){
                    $hasOrder->totalShipped = $hasOrder->totalShipped + $model->drySeedWeight;
                }
                if($modelOld->status == "In Stock"){
                    $hasOrder->totalInStock = $hasOrder->totalInStock - $modelOld->drySeedWeight;
                }else if($modelOld->status == "Shipped"){
                    $hasOrder->totalShipped = $hasOrder->totalShipped - $modelOld->drySeedWeight;
                }

                $hasOrder->save();
                }

                echo "<script>window.history.back();</script>";
            }else{
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDatetry($harvest, $extract){
        if($harvest){
            echo $extract;
        }else{
            echo "Need date in Harvest Date";
        }
    }

    /**
     * Deletes an existing Stocklist model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id = 0, $order = 0)
    {
        $model = $this->findModel($id);
        $modelSOs = StocklistHasOrderSearch::find()->andFilterWhere(['=' ,'order_idorder',$order])->one();
        $orderA = Order::findOne($order);

        if ($model->numberOfFruitsHarvested){
            $modelSOs->totalNumberOfFruitsHarvested = $modelSOs->totalNumberOfFruitsHarvested-$model->numberOfFruitsHarvested;
        }
        if($model->wetSeedWeight){
            $modelSOs->totalWetSeedWeight = $modelSOs->totalWetSeedWeight-$model->wetSeedWeight;
        }
        if ($model->drySeedWeight){
            $modelSOs->totalDrySeedWeight = $modelSOs->totalDrySeedWeight-$model->drySeedWeight;
        }
        $modelSOs->totalAvarageWeightOfSeedsPF = ($modelSOs->totalDrySeedWeight/$modelSOs->totalNumberOfFruitsHarvested);
        if ($model->numberOfBags){
            $modelSOs->totalNumberOfBags = $modelSOs->totalNumberOfBags-$model->numberOfBags;
        }
        if($model->status == "In Stock"){
            $modelSOs->totalInStock = $modelSOs->totalInStock-$model->drySeedWeight;
        }else if($model->status == "Shipped"){
            $modelSOs->totalShipped = $modelSOs->totalShipped-$model->drySeedWeight;
        }
        if($orderA->remainingPlantsF) {
            $modelSOs->avarageGP = $modelSOs->totalDrySeedWeight / $orderA->remainingPlantsF;
        }else if($orderA->remainingPlantsF){
            $modelSOs->avarageGP = $modelSOs->totalDrySeedWeight / $orderA->remainingPlantsF;
        }else{
            $modelSOs->avarageGP = $modelSOs->totalDrySeedWeight / $orderA->nurseryF;
        }
        if($modelSOs->save()){

            $model->hasOrderId = $modelSOs->order_idorder;
            $this->findModel($id)->delete();

            echo "<script>window.history.back();</script>";
        }else{
            echo "<script>window.history.back();</script>";
        }

        echo "<script>window.history.back();</script>";
    }

    /**
     * Finds the Stocklist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stocklist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stocklist::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionImportExcel(){

        $inputFile = 'uploads/stocklist1.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(2);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $crop = $order[13][1];
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $crop;
                //echo "<br><br>";
                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();


                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .

                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else {
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }


                    $stocklist->LUser = "System1";
                    $stocklistHasOrder->LUser = "System1";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }


            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else {
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }
                    //echo "<br><br>";

                    $stocklist->LUser = "System1";
                    $stocklistHasOrder->LUser = "System1";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }
    }

    public function actionImportExcel2(){

        $inputFile = 'uploads/stocklist2.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(2);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $crop = $order[13][1];
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $crop;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System2";
                    $stocklistHasOrder->LUser = "System2";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }


            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System2";
                    $stocklistHasOrder->LUser = "System2";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }
    }

    public function actionImportExcel3(){

        $inputFile = 'uploads/stocklist3.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(2);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $crop = $order[13][1];
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $crop;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System3 ";
                    $stocklistHasOrder->LUser = "System3";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }


            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System3";
                    $stocklistHasOrder->LUser = "System3";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }
    }

    public function actionImportExcel4(){

        $inputFile = 'uploads/stocklist4.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(2);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $crop = $order[13][1];
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $crop;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System4";
                    $stocklistHasOrder->LUser = "System4";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }


            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System4";
                    $stocklistHasOrder->LUser = "System4";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }
    }

    public function actionImportExcel5(){

        $inputFile = 'uploads/stocklist5.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(2);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $crop = $order[13][1];
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);

                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $crop;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();

                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System5";
                    $stocklistHasOrder->LUser = "System5";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }


            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System5";
                    $stocklistHasOrder->LUser = "System5";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }
    }

    public function actionImportExcel6(){

        $inputFile = 'uploads/stocklist6.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(2);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $crop = $order[13][1];
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $crop;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System6";
                    $stocklistHasOrder->LUser = "System6";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }


            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System6";
                    $stocklistHasOrder->LUser = "System6";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }
    }

    public function actionImportExcel7(){

        $inputFile = 'uploads/stocklist7.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(2);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $crop = $order[13][1];
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $crop;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System7";
                    $stocklistHasOrder->LUser = "System7";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }


            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System7";
                    $stocklistHasOrder->LUser = "System7";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }
    }

    public function actionImportExcel8(){

        $inputFile = 'uploads/stocklist8.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(2);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $crop = $order[13][1];
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $crop;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System8";
                    $stocklistHasOrder->LUser = "System8";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }


            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System8";
                    $stocklistHasOrder->LUser = "System8";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }
    }

    public function actionImportExcel9(){

        $inputFile = 'uploads/stocklist9.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(2);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $crop = $order[13][1];
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $crop;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][19];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System9";
                    $stocklistHasOrder->LUser = "System9";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }


            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Packing List Description: " .
                    $stocklist->packingListDescription = $rowData[0][16];
                    // echo " || Remarks seeds: " .
                   $stocklist->remarksSeeds = $rowData[0][17];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][18];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][19];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][20];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][21];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][22];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System9";
                    $stocklistHasOrder->LUser = "System9";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }

    }

    public function actionImportExcel10(){

        $inputFile = 'uploads/stocklist10.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(2);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $crop = $order[13][1];
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $crop;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][19];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System10";
                    $stocklistHasOrder->LUser = "System10";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }


            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][16];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][17];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][18];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][19];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][20];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][21];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System10";
                    $stocklistHasOrder->LUser = "System10";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }
                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }

    }

    public function actionImportExcel11(){
        $inputFile = 'uploads/stocklist11.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $if = strpos($order[13][1], ',');
                $phase = '';
                $crop = explode(',', $order[13][1])[0];
                if ($if !== false) {
                    $phase = explode('F', explode(',', $order[13][1])[1])[1];
                }
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $crop;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Packing List Description: " .
                    $stocklist->packingListDescription = $rowData[0][16];
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][17];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][18];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][19];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][20];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][21];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][22];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }
                    if($phase!=''){
                        $stocklistHasOrder->phase = $phase;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System11";
                    $stocklistHasOrder->LUser = "System11";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }

            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Packing List Description: " .
                    $stocklist->packingListDescription = $rowData[0][16];
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][17];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][18];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][19];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][20];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][21];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][22];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    if(is_numeric($stocklistHasOrder->totalWetSeedWeight) && is_numeric($stocklist->wetSeedWeight)) {
                        $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    }elseif (is_numeric($stocklist->wetSeedWeight)){
                        $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    }
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System11";
                    $stocklistHasOrder->LUser = "System11";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }
    }

    public function actionImportExcel12(){
        $inputFile = 'uploads/stocklist12.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $if = strpos($order[13][1], ',');
                $phase = '';
                $crop = explode(',', $order[13][1])[0];
                if ($if !== false) {
                    $phase = explode('F', explode(',', $order[13][1])[1])[1];
                }
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $cro  p;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Packing List Description: " .
                    $stocklist->packingListDescription = $rowData[0][16];
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][17];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][18];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][19];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][20];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][21];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][22];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }
                    if($phase!=''){
                        $stocklistHasOrder->phase = $phase;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System12";
                    $stocklistHasOrder->LUser = "System12";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }

            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Packing List Description: " .
                    $stocklist->packingListDescription = $rowData[0][16];
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][17];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][18];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][19];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][20];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][21];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][22];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    if(is_numeric($stocklistHasOrder->totalWetSeedWeight) && is_numeric($stocklist->wetSeedWeight)) {
                        $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    }elseif (is_numeric($stocklist->wetSeedWeight)){
                        $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    }
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System12";
                    $stocklistHasOrder->LUser = "System12";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }
    }

    public function actionImportExcel13(){
        $inputFile = 'uploads/stocklist13.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $if = strpos($order[13][1], ',');
                $phase = '';
                $crop = explode(',', $order[13][1])[0];
                if ($if !== false) {
                    $phase = explode('F', explode(',', $order[13][1])[1])[1];
                }
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $cro  p;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Packing List Description: " .
                    $stocklist->packingListDescription = $rowData[0][16];
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][17];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][18];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][19];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][20];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][21];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][22];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }
                    if($phase!=''){
                        $stocklistHasOrder->phase = $phase;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System13";
                    $stocklistHasOrder->LUser = "System13";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }

            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Packing List Description: " .
                    $stocklist->packingListDescription = $rowData[0][16];
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][17];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][18];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][19];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][20];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][21];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][22];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    if(is_numeric($stocklistHasOrder->totalWetSeedWeight) && is_numeric($stocklist->wetSeedWeight)) {
                        $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    }elseif (is_numeric($stocklist->wetSeedWeight)){
                        $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    }
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System13";
                    $stocklistHasOrder->LUser = "System13";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    if(!$stocklist->save()){
                        print_r($stocklist->getErrors());
                        echo $stocklist->eol;
                    }
                    if(!$stocklistHasOrder->save()){
                        print_r($stocklistHasOrder->getErrors());
                        die;
                    }

                }
                continue;
            } else {
                continue;
            }
        }
    }

    public function actionImportExcel14(){
        $inputFile = 'uploads/stocklist14.xlsx';
        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader->load($inputFile);
        }catch (Exception $e){die('Error');}
        $sheet = $objPHPEXCEL->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $orderstart = 1;
        $realOrder = "";
//        42

        for ($row = 42; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowDataPrev = $sheet->rangeToArray('A' . ($row - 1) . ':' . $highestColumn . ($row - 1), NULL, TRUE, FALSE);

            if ($rowDataPrev[0][2] != $rowData[0][2]) {
                //echo "----------------------------------------------------------------------------------------------------<br><br>";
                $orderstart = $row;

                $orderstart . " || " . $row;
                //echo "<br><br>";
                $idSHO = 0;
                $order = $sheet->rangeToArray('A' . $orderstart . ':C' . ($orderstart + 20), NULL, TRUE, FALSE);
                $orderKG = $order[3][1];
                $if = strpos($order[13][1], ',');
                $phase = '';
                $crop = explode(',', $order[13][1])[0];
                if ($if !== false) {
                    $phase = explode('F', explode(',', $order[13][1])[1])[1];
                }
                $hybrid = explode('-', $order[0][2])[0];
                $compartment = str_replace('GH ', '', $order[11][1]);
                $compartment = str_replace('Gh ', '', $compartment);
                $compartment = str_replace('gH ', '', $compartment);
                $compartment = str_replace('gh ', '', $compartment);
                //echo "Hybrid: " . $hybrid;
                //echo ' || Compartment: ' . $compartment;
                //echo ' || OrderKg: ' . $orderKG;
                //echo ' || Crop: ' . $cro  p;
                //echo "<br><br>";

                $realOrder = Order::find()
                    ->joinWith('compartmentIdCompartment')
                    ->joinWith('hybridIdHybr')
                    ->andFilterWhere(['=', 'orderKg', $orderKG])
                    ->andFilterWhere(['=', 'compartment.compNum', $compartment])
                    ->andFilterWhere(['=', 'numCrop', $crop])
                    ->andFilterWhere(['=', 'hybrid.variety', $hybrid])
                    ->one();
                if ($realOrder) {
                    //  echo "Order_ID: " . $realOrder->idorder;
                    //echo "<br><br>";
                    $idSHO = 1;
                    $stocklist = new Stocklist();
                    $stocklistHasOrder = new StocklistHasOrder();
                    //echo "Primer Registro:<br><br>";
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    //echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Packing List Description: " .
                    $stocklist->packingListDescription = $rowData[0][16];
                    // echo " || WAP: " .
                    $stocklist->wap = $rowData[0][17];
                    // echo " || Ring Color: " .
                    $stocklist->ringColor = $rowData[0][18];
                    // echo " || Fruits Color: " .
                    $stocklist->fruitColor = $rowData[0][19];
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][20];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][21];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][22];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][23];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][24];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][25];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Stocklist complete: <br><br>";

                    // echo "Order_id: " .
                    $stocklistHasOrder->order_idorder = $realOrder->idorder;
                    // echo "|| Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    if ($stocklistHasOrder->totalNumberOfFruitsHarvested == 0) {
                        continue;
                    }else {
                        $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    }
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklist->drySeedWeight;
                    }
                    if($phase!=''){
                        $stocklistHasOrder->phase = $phase;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }


                    $stocklist->LUser = "System14";
                    $stocklistHasOrder->LUser = "System14";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->stocklist_idstocklist = $stocklist->idstocklist;
                    $stocklistHasOrder->save();

//                    echo "<br><br>";
                    continue;

                } else {
                    echo "Hybrid: " . $hybrid;
                    echo ", No Order found.<br><br>";
//                    echo "----------------------------------------------------------------------------------------------------<br><br>";
                    $idSHO = 0;
                    continue;
                }
            }
            if ($row == 42) {
                $orderstart = $row;
            }

            if ($realOrder != "") {
                $stocklistHasOrder = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $realOrder->idorder])->one();
                if ($stocklistHasOrder) {
                    if ($rowData[0][5] == "" && $rowData[0][7] == ""){
                        continue;
                    }

                    $stocklist = new Stocklist();
                    // echo "Harvest N°: " .
                    $stocklist->harvestNumber = $rowData[0][3];
                    // echo " || Harvest Date: " .
                    $stocklist->harvestDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][4]));
                    // echo " || Number of fruits harvest: " .
                    $stocklist->numberOfFruitsHarvested = $rowData[0][6];
                    // echo " || Clening Date: " .
                    $stocklist->cleaningDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])));
                    // echo " || Wet Seed Weight: " .
                    $stocklist->wetSeedWeight = $rowData[0][9];
                    // echo " || Dry Seed Weight: " .
                    $stocklist->drySeedWeight = $rowData[0][10];
                    // echo " || Average weight of seeds per fruit: " .
                    $stocklist->avgWeightOfSeedPF = $rowData[0][12];
                    // echo " || Numbers Of Bags: " .
                    $stocklist->numberOfBags = $rowData[0][13];
                    // echo " || Carton No: " .
                    $stocklist->cartonNo = $rowData[0][14];
                    // echo " || Shipment Date: " .
                    $stocklist->shipmentDate = date("Y-m-d", (\PHPExcel_Shared_Date::ExcelToPHP($rowData[0][15])));
                    // echo " || Packing List Description: " .
                    $stocklist->packingListDescription = $rowData[0][16];
                    // echo " || WAP: " .
                    $stocklist->wap = $rowData[0][17];
                    // echo " || Ring Color: " .
                    $stocklist->ringColor = $rowData[0][18];
                    // echo " || Fruits Color: " .
                    $stocklist->fruitColor = $rowData[0][19];
                    // echo " || Remarks seeds: " .
                    $stocklist->remarksSeeds = $rowData[0][20];
                    // echo " || Destroyed: " .
                    $stocklist->destroyed = $rowData[0][21];
                    // echo " || Moisture: " .
                    $stocklist->moisture = $rowData[0][22];
                    // echo " || TSW: " .
                    $stocklist->tsw = $rowData[0][23];
                    // echo " || EOL: " .
                    $stocklist->eol = $rowData[0][24];
                    // echo " || Status: " .
                    $stocklist->status = $rowData[0][25];
                    // echo " || Order-Id: " .
                    $stocklist->hasOrderId = $realOrder->idorder;
                    // echo "<br><br>Complete, ";

                    // echo "<br><br>";
                    // echo "Total_number_of_fruits_harvest: " .
                    $stocklistHasOrder->totalNumberOfFruitsHarvested = $stocklistHasOrder->totalNumberOfFruitsHarvested + $stocklist->numberOfFruitsHarvested;
                    // echo "|| Total_wet_s_w: " .
                    if(is_numeric($stocklistHasOrder->totalWetSeedWeight) && is_numeric($stocklist->wetSeedWeight)) {
                        $stocklistHasOrder->totalWetSeedWeight = $stocklistHasOrder->totalWetSeedWeight + $stocklist->wetSeedWeight;
                    }elseif (is_numeric($stocklist->wetSeedWeight)){
                        $stocklistHasOrder->totalWetSeedWeight = $stocklist->wetSeedWeight;
                    }
                    // echo "|| Total_dry_s_w: " .
                    $stocklistHasOrder->totalDrySeedWeight = $stocklistHasOrder->totalDrySeedWeight + $stocklist->drySeedWeight;
                    // echo "|| Total_avg_w_s_pf: " .
                    $stocklistHasOrder->totalAvarageWeightOfSeedsPF = ($stocklistHasOrder->totalDrySeedWeight / $stocklistHasOrder->totalNumberOfFruitsHarvested);
                    // echo "|| Total_num_bags: " .
                    $stocklistHasOrder->totalNumberOfBags = $stocklistHasOrder->totalNumberOfBags + $stocklist->numberOfBags;
                    if ($stocklist->status == "Shipped") {
                        // echo "|| Total_shipped: " .
                        $stocklistHasOrder->totalShipped = $stocklistHasOrder->totalShipped + $stocklist->drySeedWeight;
                    } else if ($stocklist->status == "In Stock") {
                        // echo "|| Total_in_stock: " .
                        $stocklistHasOrder->totalInStock = $stocklistHasOrder->totalInStock + $stocklist->drySeedWeight;
                    }

                    if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->remainingPlantsF) {
                        // echo "|| avg_gp: " .
                        $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->remainingPlantsF;
                    } else if ($realOrder->nurseryF > 0){
                        // echo "|| avg_gp: " .
                        if ($realOrder->nurseryF != 0){
                            $stocklistHasOrder->avarageGP = $stocklistHasOrder->totalDrySeedWeight / $realOrder->nurseryF;
                        }else{
                            $stocklistHasOrder->avarageGP = 0;
                        }
                    }else{
                        $stocklistHasOrder->avarageGP = 0;
                    }
                    //echo "<br><br>";


                    $stocklist->LUser = "System14";
                    $stocklistHasOrder->LUser = "System14";
                    if(!is_numeric($stocklist->moisture)){
                        $stocklist->moisture = null;
                    }

                    $stocklist->save();
                    $stocklistHasOrder->save();
                }
                continue;
            } else {
                continue;
            }
        }
    }

}
