<?php

namespace backend\controllers;

use backend\models\StocklistSearch;
use Yii;
use backend\models\StocklistHasOrder;
use backend\models\StocklistHasOrderSearch;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Stocklist;
use backend\models\Order;
use yii2mod\rbac\filters\AccessControl;

/**
 * StocklistHasOrderController implements the CRUD actions for StocklistHasOrder model.
 */
class StocklistHasOrderController extends Controller
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
                        'actions' => ['create', 'update', 'delete', 'sent'],
                        'allow' => true,
                        'roles' => ['Administrator', 'Estimator', 'Estimator Helper'],
                    ],
                    [
                        'actions' => ['index', 'view',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
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
     * Lists all StocklistHasOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StocklistHasOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

//        $data


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StocklistHasOrder model.
     * @param integer $idstocklist_has_order
     * @param integer $stocklist_idstocklist
     * @param integer $order_idorder
     * @return mixed
     */
    public function actionView($idstocklist_has_order, $stocklist_idstocklist, $order_idorder)
    {

        $searchModel = new StocklistSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['=', 'hasOrderId', $order_idorder])
            ->all();

        return $this->render('view', [
            'model' => $this->findModel($idstocklist_has_order, $stocklist_idstocklist, $order_idorder),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'order' => $order_idorder,
        ]);
    }

    /**
     * Creates a new StocklistHasOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StocklistHasOrder();
        $modelSL = new Stocklist();

        if ($model->load(Yii::$app->request->post()) &&  $modelSL->load(Yii::$app->request->post())) {

            if($modelSL->harvestDate) {
                $modelSL->harvestDate = date('Y-m-d', strtotime($modelSL->harvestDate));
            }
            if($modelSL->cleaningDate ) {
                $modelSL->cleaningDate = date('Y-m-d', strtotime($modelSL->cleaningDate));
            }

            if($modelSL->drySeedWeight && $modelSL->numberOfFruitsHarvested) {
                $modelSL->avgWeightOfSeedPF = ($modelSL->drySeedWeight / $modelSL->numberOfFruitsHarvested);
            }
            if ($modelSL->packingListDescription == 'Last harvest'){
                $modelSL->eol = "Yes";
            }else{
                $modelSL->eol = "No";
            }
            if($modelSL->drySeedWeight){
                $modelSL->status = "In Stock";
            }
            if(!$modelSL->status){
                $modelSL->status = "Incomplete";
            }

            $orderA = Order::findOne($model->order_idorder);

            $model->totalNumberOfFruitsHarvested = $modelSL->numberOfFruitsHarvested;

            $model->totalWetSeedWeight = $modelSL->wetSeedWeight;

            $model->totalDrySeedWeight = $modelSL->drySeedWeight;

            $model->totalAvarageWeightOfSeedsPF = ($model->totalDrySeedWeight/$model->totalNumberOfFruitsHarvested);
            if($modelSL->status == "In Stock"){
                $model->totalInStock = $modelSL->drySeedWeight;
            }else if($modelSL->status == "Shipped"){
                $model->totalShipped = $modelSL->drySeedWeight;
            }
            if($orderA->remainingPlantsF) {
                $model->avarageGP = $model->totalDrySeedWeight / $orderA->remainingPlantsF;
            }else if($orderA->remainingPlantsF){
                $model->avarageGP = $model->totalDrySeedWeight / $orderA->remainingPlantsF;
            }else{
                $model->avarageGP = $model->totalDrySeedWeight / $orderA->nurseryF;
            }

            $modelSL->hasOrderId = $model->order_idorder;
            $modelSL->LUser = Yii::$app->user->identity->username;
            $model->LUser = Yii::$app->user->identity->username;



            if ($modelSL->save()) {
                $model->stocklist_idstocklist = $modelSL->idstocklist;
                if ($model->save()) {
                    echo "<script>window.history.back();</script>";
                    die;
                }else{
                    print_r($model->getErrors());
                    die;
                    echo "<script>window.history.back();</script>";
                    die;
                }
            }else{
                print_r($modelSL->getErrors());
                die;
                echo "<script>window.history.back();</script>";
                die;
            }

        } else {
            $this->layout = 'main2';
            return $this->render('create', [
                'model' => $model,
                'modelSL' => $modelSL,
            ]);
        }
    }


    /**
     * Updates an existing StocklistHasOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $idstocklist_has_order
     * @param integer $stocklist_idstocklist
     * @param integer $order_idorder
     * @return mixed
     */
    public function actionUpdate($idstocklist_has_order, $stocklist_idstocklist, $order_idorder)
    {
        $model = $this->findModel($idstocklist_has_order, $stocklist_idstocklist, $order_idorder);
        $modelOld = $this->findModel($idstocklist_has_order, $stocklist_idstocklist, $order_idorder);

        if ($model->load(Yii::$app->request->post())) {
            $model->LUser = Yii::$app->user->identity->username;
            if ($model->save()) {
                if ($model->order_idorder != $modelOld->order_idorder) {
                    $stocklists = Stocklist::find()->andFilterWhere(['=', 'hasOrderId', $order_idorder])->all();

                    foreach ($stocklists AS $stocklist) {
                        $stocklist->hasOrderId = $model->order_idorder;
                        $stocklist->save();
                    }
                }
                echo "<script>window.history.back();</script>";
                die;
            }else{
                print_r($model->getErrors());
            }
            } else {
                return $this->renderAjax('update', [
                    'model' => $model,
                ]);
            }
    }

    /**
     * Deletes an existing StocklistHasOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $idstocklist_has_order
     * @param integer $stocklist_idstocklist
     * @param integer $order_idorder
     * @return mixed
     */
    public function actionDelete($idstocklist_has_order, $stocklist_idstocklist, $order_idorder)
    {
        $this->findModel($idstocklist_has_order, $stocklist_idstocklist, $order_idorder)->delete();

        $stocklists = Stocklist::find()->andFilterWhere(['=', 'hasOrderId', $order_idorder])->all();

        foreach ($stocklists AS $stocklist) {
            $stocklist->delete();
        }

        echo "<script>window.history.back();</script>";
        die;
    }

    /**
     * Finds the StocklistHasOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $idstocklist_has_order
     * @param integer $stocklist_idstocklist
     * @param integer $order_idorder
     * @return StocklistHasOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($idstocklist_has_order, $stocklist_idstocklist, $order_idorder)
    {
        if (($model = StocklistHasOrder::findOne(['idstocklist_has_order' => $idstocklist_has_order, 'stocklist_idstocklist' => $stocklist_idstocklist, 'order_idorder' => $order_idorder])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    function actionSent($date = 0)
    {
        if ($date == 0) {
            $date = date('Y-m-d');
        }

        $searchModel = new StocklistSearch();
        $dataProvider = Stocklist::find()->andFilterWhere(['=', 'shipmentDate', $date])->orderBy('cartonNo')->all();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query = StocklistSearch::find();
        $dataProvider->query->select(['idstocklist', 'cleaningDate', 'harvestDate', 'cartonNo', 'hasOrderId', 'SUM(drySeedWeight) AS drySeedWeight', 'packingListDescription', 'AVG(moisture) AS moisture', 'eol', 'SUM(numberOfBags) AS numberOfBags'])->andFilterWhere(['=', 'shipmentDate', $date])->groupBy('cartonNo, packingListDescription, hasOrderId')->orderBy('cartonNo')->all();


        $stocklists = Stocklist::find()->select(['idstocklist', 'cartonNo', 'moisture', 'hasOrderId', 'drySeedWeight', 'packingListDescription', 'moisture', 'eol', 'numberOfBags'])->andFilterWhere(['=', 'shipmentDate', $date])->orderBy('cartonNo')->all();

        $totals = array();
        foreach ($stocklists AS $stocklist) {
            if (empty($totals['bags'][$stocklist->cartonNo])) {
                $totals['bags'][$stocklist->cartonNo] = $stocklist->numberOfBags;
            } else {
                $totals['bags'][$stocklist->cartonNo] = $totals['bags'][$stocklist->cartonNo] + $stocklist->numberOfBags;
            }
            if (empty($totals['net'][$stocklist->cartonNo])) {
                $totals['net'][$stocklist->cartonNo] = $stocklist->drySeedWeight;
            } else {
                $totals['net'][$stocklist->cartonNo] = $totals['net'][$stocklist->cartonNo] + $stocklist->drySeedWeight;
            }
        }

/*        $connection = Yii::$app->getDb();
        $query  = 'SELECT `idstocklist`, `idHybrid` , `cleaningDate`, `harvestDate`, `cartonNo`, `hasOrderId`, SUM(drySeedWeight) AS drySeedWeight, `packingListDescription`, AVG(moisture) AS moisture, `eol`, SUM(numberOfBags) AS numberOfBags FROM `stocklist` `s` INNER JOIN `order` `o` INNER JOIN `hybrid` `h` WHERE `shipmentDate` = \''.$date.'.\' AND `hasOrderId` = `idorder` AND `Hybrid_idHybrid` = `idHybrid` GROUP BY `cartonNo`, `packingListDescription`, `hasOrderId` ORDER BY `cartonNo`, `packingListDescription`, `moisture`';
        $command = $connection->createCommand($query);
        $stocklists2 = $command->queryAll();*/
        $stocklists2 = Stocklist::find()->select(['idstocklist', 'idHybrid', 'cleaningDate', 'harvestDate', 'cartonNo', 'hasOrderId', 'SUM(drySeedWeight) AS drySeedWeight', 'packingListDescription', 'AVG(moisture) AS moisture', 'eol', 'SUM(numberOfBags) AS numberOfBags'])->innerJoin('order')->innerJoin('hybrid')->andWhere('hasOrderId = idorder')->andWhere('Hybrid_idHybrid = idHybrid')->andWhere(['=', 'shipmentDate', $date])->groupBy('cartonNo, packingListDescription, hasOrderId')->orderBy('cartonNo, idHybrid, packingListDescription, moisture')->all();

        $grid = array();

        $lastElement = end($stocklists2);

        $i= 0;
        $bags = 0;
        $nets = 0;
        foreach($totals AS $total){
            if ($i == 0) {
                foreach($total AS $i => $to){
                    $bags = $bags + $to;
                }

            }else {
                foreach($total AS $i => $to){
                    $nets = $nets + $to;
                }
            }
            $i++;
        }

        $lotnr = '';
        foreach ($stocklists2 AS $index => $stocklist ){
            if ($index > 0) {

                // CartonNo
                $prevcartonNo = $stocklists2[$index - 1]->cartonNo;
                if ($stocklist->cartonNo != $prevcartonNo) {
                    $cartonNo = $prevcartonNo." Total";

                    // Add to the array
                    $grid[] = ['cartonNo' => $cartonNo, 'variety' => "", 'lotNr' => "", 'compCP' => "", 'packLD' => "", 'moisture' => "", 'eol' => "", 'numOfBags' => $totals['bags'][$prevcartonNo], "drySeedWeight" => round($totals['net'][$prevcartonNo]/1000, 3)];

                    $cartonNo =  $stocklist->cartonNo;
                } else {
                    $cartonNo = "";
                }

                //Lot NR
                if(StocklistHasOrder::find(['=', 'order_idorder', $stocklist->hasOrderId])->one()) {
                    if (StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $stocklists2[$index-1]->hasOrderId])->one()->lotNr == StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $stocklist->hasOrderId])->one()->lotNr) {
                        $lotnr = '';
                    } else {
                        $lotnr = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $stocklist->hasOrderId])->one()->lotNr;
                    }
                }

                //Compartment + Crop + Phase
                if (StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $stocklists2[$index-1]->hasOrderId])->one()->lotNr == StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $stocklist->hasOrderId])->one()->lotNr) {
                    $compCP = '';
                }else{
                    $compCP = Order::findOne(['=', 'idorder', $stocklist->hasOrderId])->compartmentIdCompartment->compNum.', '.Order::findOne(['=', 'idorder', $stocklist->hasOrderId])->numCrop.', F'.StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $stocklist->hasOrderId])->one()->phase;
                }

                // Variedad
                $order = Order::find()->andFilterWhere(['=', 'idorder', $stocklist->hasOrderId])->one()->idorder;
                $preOrder = Order::find()->andFilterWhere(['=', 'idorder', $stocklists2[$index-1]->hasOrderId])->one()->idorder;

                if ($preOrder == $order) {
                    $variety = '';
                } else {
                    $variety = Order::findOne(['=', 'idorder', $stocklist->hasOrderId])->hybridIdHybr->variety;
                }

                // Add to the array
                $grid[] = ['cartonNo' => $cartonNo, 'variety' => $variety, 'lotNr' => $lotnr, 'compCP' => $compCP, 'packLD' => $stocklist->packingListDescription, 'moisture' => round($stocklist->moisture, 3), 'eol' => $stocklist->eol, 'numOfBags' => $stocklist->numberOfBags, "drySeedWeight" => round($stocklist->drySeedWeight/1000, 3)];


            }else{
                //CartonNo
                $cartonNo =  $stocklist->cartonNo;

                // Variedad

                $variety = Order::findOne(['=', 'idorder', $stocklist->hasOrderId])->hybridIdHybr->variety;

                //Lot NR

                if(StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $stocklist->hasOrderId])->one()){
                    $lotnr = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $stocklist->hasOrderId])->one()->lotNr;
                }

                //Compartment + Crop + Phase

                if (StocklistHasOrder::find()->andFilterWhere(['=',  'order_idorder', $stocklist->hasOrderId])->one()->phase){
                    $compCP = Order::findOne(['=', 'idorder', $stocklist->hasOrderId])->compartmentIdCompartment->compNum.', '.Order::findOne(['=', 'idorder', $stocklist->hasOrderId])->numCrop.', F'.StocklistHasOrder::find()->andFilterWhere(['=',  'order_idorder', $stocklist->hasOrderId])->one()->phase;
                }else{
                    $compCP = Order::findOne(['=', 'idorder', $stocklist->hasOrderId])->compartmentIdCompartment->compNum.', '.Order::findOne(['=', 'idorder', $stocklist->hasOrderId])->numCrop;
                }

                // Add to the array
                $grid[] = ['cartonNo' => $cartonNo, 'variety' => $variety, 'lotNr' => $lotnr, 'compCP' => $compCP, 'packLD' => $stocklist->packingListDescription, 'moisture' => round($stocklist->moisture, 3), 'eol' => $stocklist->eol, 'numOfBags' => $stocklist->numberOfBags, "drySeedWeight" => round($stocklist->drySeedWeight/1000, 3)];
            }
            if($stocklist == $lastElement && sizeof($stocklists2) > 1) {
                // Add to the array
                $grid[] = ['cartonNo' => $stocklist->cartonNo." Total", 'variety' => "", 'lotNr' => "", 'compCP' => "", 'packLD' => "", 'moisture' => "", 'eol' => "", 'numOfBags' => $totals['bags'][$prevcartonNo], "drySeedWeight" => round($totals['net'][$prevcartonNo]/1000, 3)];
                // Add to the array
                $grid[] = ['cartonNo' => "Grand Total", 'variety' => "", 'lotNr' => "", 'compCP' => "", 'packLD' => "", 'moisture' => "", 'eol' => "", 'numOfBags' => $bags, "drySeedWeight" => round($nets/1000, 3)];
            }else if($stocklist == $lastElement){
                // Add to the array
                $grid[] = ['cartonNo' => "Grand Total", 'variety' => "", 'lotNr' => "", 'compCP' => "", 'packLD' => "", 'moisture' => "", 'eol' => "", 'numOfBags' => $bags, "drySeedWeight" => round($nets/1000, 3)];
            }
        }
        $provider = new ArrayDataProvider([
            'allModels' => $grid,
            'pagination' => [
                 'pageSize' => 10000,
            ],
            'sort' => [
//                'attributes' => ['cartonNo', 'variety', 'lotNr', 'compCP', 'packLD', 'moisture', 'eol', 'numOfBags', "drySeedWeight"],
            ]
        ]);
        return $this->render('sent', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totals' => $totals,
            'date' => $date,
            'grid' => $grid,
            'provider' => $provider,
        ]);
    }
}
