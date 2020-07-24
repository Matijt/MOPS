<?php

namespace backend\controllers;

use backend\models\Order;
use backend\models\Pollen;
use backend\models\Registry;
use backend\models\StocklistHasOrder;
use Yii;
use backend\models\Estimations;
use backend\models\EstimationsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2mod\rbac\filters\AccessControl;

/**
 * EstimationsController implements the CRUD actions for Estimations model.
 */
class EstimationsController extends Controller
{
    /**
     * {@inheritdoc}
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
                        'actions' => ['create', 'update', 'delete', 'history1', 'history2'],
                        'allow' => true,
                        'roles' => ['Administrator', 'Estimator'],
                    ],
                    [
                        'actions' => ['history1', 'history2'],
                        'allow' => true,
                        'roles' => ['Estimator Helper'],
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
     * Lists all Estimations models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EstimationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Estimations model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Estimations model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Estimations();

        if ($model->load(Yii::$app->request->post())) {
            echo "Total Females Count: ";
            $registries = Registry::find()->andFilterWhere(['=', 'order_idorder', $model->order_idorder])->all();
            $totalFemalesCount = 0;
            $totalFemalesCount2 = 0;
            $totalPlantsChecked = 0;
            foreach ($registries AS $registry){
                if($registry->fruitsCount != 0){
                    $totalFemalesCount = $registry->quantity;
                    $totalFemalesCount2 = $registry->quantity2;
                    $totalPlantsChecked = $registry->fruitsCount;
                }else {
                    $totalFemalesCount = $totalFemalesCount + $registry->quantity;
                    $totalPlantsChecked = $totalPlantsChecked + 5;
                }
            }
            echo $model->totalFemalesCount= $totalFemalesCount;
            echo "<br><br>Total Plants checked: ";
            echo $model->totalPlantsCheked = $totalPlantsChecked;
            echo "<br><br>";



                // Total de hembras contados es el total de frutos contados.
                echo "Plantas totales: ";
                if ($model->orderIdorder->realisedNrOfPlantsF != null) {
                    if ($model->orderIdorder->realisedNrOfPlantsF > 0) {
                        echo $model->plantsTotal = $model->orderIdorder->realisedNrOfPlantsF;
                    } else {
                        echo $model->plantsTotal = $model->orderIdorder->netNumOfPlantsF;
                    }
                } else {
                    echo $model->plantsTotal = $model->orderIdorder->netNumOfPlantsF;
                }
                echo "<br><br>";

            if(substr($model->orderIdorder->hybridIdHybr->variety, 0, 1) == "T") {
                echo "Gramos por fruta: ";
                echo $model->gramPerFruit = $model->inStock / $model->fruitsHarvest;
                echo "<br><br>";

                echo "frutos en planta: ";
                echo $model->fruitsInPlant = ($model->totalFemalesCount / $model->totalPlantsCheked) * $model->plantsTotal;
                echo "<br><br>";

                echo "Gramos en planta: ";
                echo $model->gramsInPlant = $model->gramPerFruit * $model->fruitsInPlant;
                echo "<br><br>";

                echo "Total harvest + Setted: ";
                echo $model->totalHarvestS = ($model->inStock + $model->gramsInPlant) / 1000;
                echo "<br><br>";

                echo $model->fecha = date('Y-m-d', strtotime($model->fecha));
                echo "<br><br>";
                echo "Dias de pollinización: ";
                $now = strtotime($model->fecha);
                $your_date = strtotime($model->orderIdorder->pollinationF);
                $datediff = $now - $your_date;

                echo $model->pollinationDays = round($datediff / (60 * 60 * 24)) - 7;
                echo "<br><br>";

                echo "Frutos promedio por día: ";
                echo $model->fruitsAvgPerDay = ($model->fruitsHarvest + $model->fruitsInPlant) / $model->pollinationDays;
                echo "<br><br>";

                echo "Dias de pollinización extra: ";
                $now = strtotime($model->fecha);
                $your_date = strtotime($model->orderIdorder->pollinationU);
                $datediff = $your_date - $now;

                echo $model->extraPollination = round($datediff / (60 * 60 * 24));

                if ($model->extraPollination < 0 ){
                    $model->extraPollination = 0;
                }
                echo $model->extraPollination;
                echo "<br><br>";

                echo "Frutos to be setted: ";
                echo $model->fruitsToBeSetted = $model->fruitsAvgPerDay * $model->extraPollination;
                echo "<br><br>";

                echo "Grams to be setted: ";
                echo $model->gramsToBeSetted = $model->fruitsToBeSetted * $model->gramPerFruit;
                echo "<br><br>";

                echo "Factor menos: ".$model->factorLess."%";
                echo "<br><br>";


                echo "Grams real to be setted: ";
                echo $model->gramsRealToBeSetted = ($model->gramsToBeSetted * (100-$model->factorLess))/100;
                echo "<br><br>";

                echo "Total harvest: ";
                echo $model->totalHarvest = ($model->gramsRealToBeSetted + $model->gramsInPlant + $model->inStock) / 1000;
                echo "<br><br>";

                echo "Order: " . $model->orderIdorder->orderKg;
                echo "<br><br>";
                echo "Order ID: " . $model->orderIdorder->idorder;
                echo "<br><br>";
                //1861

                echo "Difference: ";
                echo $model->difference = round((($model->totalHarvest - $model->orderIdorder->orderKg) / $model->orderIdorder->orderKg) * 100);

                if ($model->avgGrsPlant) {
                    echo $model->totalEstimatedProduction = $model->avgGrsPlant * $model->plantsTotal;
                }
            }else{
                echo "Frutos promedio 1: ";
                echo $model->avgFruits1 = $totalFemalesCount/$totalPlantsChecked;
                echo "<br><br>";

                echo "Frutos estimado 1: ";
                echo $model->fruitsEstimated1 = $model->avgFruits1*$model->plantsTotal;
                echo "<br><br>";

                echo "Gramos estimados 1: ";
                echo $model->gramsEstimated1 = $model->fruitsEstimated1*$model->gramPerFruit;
                echo "<br><br>";

            }

            $model->fecha = date('Y-m-d', strtotime($model->fecha));

            $model->LUser = Yii::$app->user->identity->username;
            if ($model->save()) {
                echo "<script>window.history.back();</script>";
            }else{
                print_r($model->getErrors());
            }
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Estimations model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            echo "Total Females Count: ";
            $registries = Registry::find()->andFilterWhere(['=', 'order_idorder', $model->order_idorder])->all();
            $totalFemalesCount = 0;
            $totalFemalesCount2 = 0;
            $totalPlantsChecked = 0;
            foreach ($registries AS $registry){
                if($registry->fruitsCount != 0){
                    $totalFemalesCount = $registry->quantity;
                    $totalFemalesCount2 = $registry->quantity2;
                    $totalPlantsChecked = $registry->fruitsCount;
                }else {
                    $totalFemalesCount = $totalFemalesCount + $registry->quantity;
                    $totalPlantsChecked = $totalPlantsChecked + 5;
                }
            }
            echo $model->totalFemalesCount= $totalFemalesCount;
            echo "<br><br>Total Plants checked: ";
            echo $model->totalPlantsCheked = $totalPlantsChecked;
            echo "<br><br>";



            // Total de hembras contados es el total de frutos contados.
            echo "Plantas totales: ";
            if ($model->orderIdorder->realisedNrOfPlantsF != null) {
                if ($model->orderIdorder->realisedNrOfPlantsF > 0) {
                    echo $model->plantsTotal = $model->orderIdorder->realisedNrOfPlantsF;
                } else {
                    echo $model->plantsTotal = $model->orderIdorder->netNumOfPlantsF;
                }
            } else {
                echo $model->plantsTotal = $model->orderIdorder->netNumOfPlantsF;
            }
            echo "<br><br>";

            if(substr($model->orderIdorder->hybridIdHybr->variety, 0, 1) == "T") {
                echo "Gramos por fruta: ";
                echo $model->gramPerFruit = $model->inStock / $model->fruitsHarvest;
                echo "<br><br>";

                echo "frutos en planta: ";
                echo $model->fruitsInPlant = ($model->totalFemalesCount / $model->totalPlantsCheked) * $model->plantsTotal;
                echo "<br><br>";

                echo "Gramos en planta: ";
                echo $model->gramsInPlant = $model->gramPerFruit * $model->fruitsInPlant;
                echo "<br><br>";

                echo "Total harvest + Setted: ";
                echo $model->totalHarvestS = ($model->inStock + $model->gramsInPlant) / 1000;
                echo "<br><br>";

                echo $model->fecha = date('Y-m-d', strtotime($model->fecha));
                echo "<br><br>";
                echo "Dias de pollinización: ";
                $now = strtotime($model->fecha);
                $your_date = strtotime($model->orderIdorder->pollinationF);
                $datediff = $now - $your_date;

                echo $model->pollinationDays = round($datediff / (60 * 60 * 24)) - 7;
                echo "<br><br>";

                echo "Frutos promedio por día: ";
                echo $model->fruitsAvgPerDay = ($model->fruitsHarvest + $model->fruitsInPlant) / $model->pollinationDays;
                echo "<br><br>";

                echo "Dias de pollinización extra: ";
                $now = strtotime($model->fecha);
                $your_date = strtotime($model->orderIdorder->pollinationU );
                $datediff = $your_date - $now;

                $model->extraPollination = round($datediff / (60 * 60 * 24));

                if ($model->extraPollination < 0 ){
                    $model->extraPollination = 0;
                }
                echo $model->extraPollination;
                echo "<br><br>";

                echo "Frutos to be setted: ";
                echo $model->fruitsToBeSetted = $model->fruitsAvgPerDay * $model->extraPollination;
                echo "<br><br>";

                echo "Grams to be setted: ";
                echo $model->gramsToBeSetted = $model->fruitsToBeSetted * $model->gramPerFruit;
                echo "<br><br>";

                echo "Factor menos: ".$model->factorLess."%";
                echo "<br><br>";

                echo "Grams real to be setted: ";
                echo $model->gramsRealToBeSetted = ($model->gramsToBeSetted * (100-$model->factorLess))/100;
                echo "<br><br>";

                echo "Total harvest: ";
                echo $model->totalHarvest = ($model->gramsRealToBeSetted + $model->gramsInPlant + $model->inStock) / 1000;
                echo "<br><br>";

                echo "Order: " . $model->orderIdorder->orderKg;
                echo "<br><br>";
                echo "Order ID: " . $model->orderIdorder->idorder;
                echo "<br><br>";
                //1861

                echo "Difference: ";
                echo $model->difference = round((($model->totalHarvest - $model->orderIdorder->orderKg) / $model->orderIdorder->orderKg) * 100);


                $model->avgGrsPlant =  $model->gramsRealToBeSetted / ($model->orderIdorder->realisedNrOfPlantsF);

                if ($model->avgGrsPlant) {
                    echo $model->totalEstimatedProduction = $model->avgGrsPlant * $model->plantsTotal;
                }
            }else {
                echo "Frutos promedio 1: ";
                echo $model->avgFruits1 = $totalFemalesCount / $totalPlantsChecked;
                echo "<br><br>";

                echo "Frutos estimado 1: ";
                echo $model->fruitsEstimated1 = $model->avgFruits1 * $model->plantsTotal;
                echo "<br><br>";

                echo "Gramos estimados 1: ";
                echo $model->gramsEstimated1 = $model->fruitsEstimated1 * $model->gramPerFruit;
                echo "<br><br>";

                echo "Gramos Set 1: ";
                echo $model->gramsSet1;
                echo "<br><br>";

                if ($model->gramPerFruit2 != 0) {


                    $registries = Registry::find()->andFilterWhere(['=', 'order_idorder', $model->order_idorder])->all();
                    $totalFemalesCount = 0;
                    $totalFemalesCount2 = 0;
                    $totalPlantsChecked = 0;
                    foreach ($registries AS $registry) {
                        if ($registry->fruitsCount != 0) {
                            $totalFemalesCount = $registry->quantity;
                            $totalFemalesCount2 = $registry->quantity2;
                            $totalPlantsChecked = $registry->fruitsCount;
                        } else {
                            $totalFemalesCount = $totalFemalesCount + $registry->quantity;
                            $totalPlantsChecked = $totalPlantsChecked + 5;
                        }
                    }
                    echo $model->totalFemalesCount2 = $totalFemalesCount2;

                    echo "Frutos promedio 2: ";
                    echo $model->avgFruits2 = $totalFemalesCount2 / $totalPlantsChecked;
                    echo "<br><br>";

                    echo "Frutos estimado 2: ";
                    echo $model->fruitsEstimated2 = $model->avgFruits2 * $model->plantsTotal;
                    echo "<br><br>";

                    echo "Gramos estimados 2: ";
                    echo $model->gramsEstimated2 = $model->fruitsEstimated2 * $model->gramPerFruit2;
                    echo "<br><br>";


                    echo "Gramos estimados final: ";
                    echo $model->gramsEstimated3 = $model->gramsEstimated2 + $model->gramsSet1;
                    echo "<br><br>";

                    echo "Gramos Set 2: ";
                    echo $model->gramsSet2;
                    echo "<br><br>";
                }

                if ($model->gramsSet2) {

                    echo "Gramos Real final: ";
                    echo $model->gramsSetFinal = $model->gramsSet2 + $model->gramsSet1;
                    echo "<br><br>";

                }


                if ($model->gramsSetFinal != null && $model->gramsSetFinal > 0){
                       $model->avgGrsPlant =  $model->gramsSetFinal / ($model->orderIdorder->realisedNrOfPlantsF);
                }
            }


            $model->fecha = date('Y-m-d', strtotime($model->fecha));

            $model->LUser = Yii::$app->user->identity->username;

            if ($model->save()) {
                echo "<script>window.history.back();</script>";
                die;
            }else{
                print_r($model->getErrors());
            }
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Estimations model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Estimations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Estimations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Estimations::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }



    public function actionHistory1($id)
    {
        if($id) {
            $order = Order::findOne($id);
            $pastEstimations = Estimations::find()
                ->joinWith('orderIdorder')
                ->andFilterWhere(['=', 'order.Hybrid_idHybrid', $order->Hybrid_idHybrid])->all();

            $countPastEstimations = Estimations::find()
                ->joinWith('orderIdorder')
                ->andFilterWhere(['=', 'order.Hybrid_idHybrid', $order->Hybrid_idHybrid])->count();

            if ($pastEstimations != null) {
                $GPF = 0;
                foreach ($pastEstimations AS $pastEstimation){
                    $GPF = $GPF + $pastEstimation->gramPerFruit;
                }
                $GPF = $GPF/$countPastEstimations;


                echo "<option value='".$GPF."'>".$GPF. ", g/f 1 </option>";
            }else{
                echo "<option value='0'>No data </option>";
            }
        }
    }


    public function actionHistory2($id)
    {
        if($id) {
            $order = Order::findOne($id);
//            echo ' <input type="text" id="Hola" name="myText2" value="" selectBoxOptions="';
            $pastEstimations = Estimations::find()
                ->joinWith('orderIdorder')
                ->andFilterWhere(['=', 'order.Hybrid_idHybrid', $order->Hybrid_idHybrid])->all();

            $countPastEstimations = Estimations::find()
                ->joinWith('orderIdorder')
                ->andFilterWhere(['=', 'order.Hybrid_idHybrid', $order->Hybrid_idHybrid])->count();

            if ($pastEstimations != null) {
                echo $pastEstimations[0]->order_idorder;
                $GPF = 0;
                foreach ($pastEstimations AS $pastEstimation){
                    $GPF = $GPF + $pastEstimation->gramPerFruit2;
                }
                $GPF = $GPF/$countPastEstimations;


                echo "<option value='".$GPF."'>".$GPF. ", g/p </option>";
            }else{
                echo "<option value='0'>No data </option>";
            }
        }
    }
}
