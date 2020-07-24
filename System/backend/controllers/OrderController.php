<?php

namespace backend\controllers;

use backend\models\Compartment;
use backend\models\Estimations;
use backend\models\Father;
use backend\models\Hybrid;
use backend\models\Registry;
use Yii;
use backend\models\NumcropHasCompartment;
use backend\models\Numcrop;
use backend\models\Order;
use backend\models\Mother;
use backend\models\OrderSearch;
use yii\bootstrap\Html;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\codigo\Facil;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\data\SqlDataProvider;
use kartik\export\ExportMenu;

/**
 * OrdersController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
                        'actions' => ['create', 'update', 'updates', 'delete', 'orderhistory', 'history', 'historyf', 'historym', 'compartment', 'compartmentpc', 'setdate', 'change', 'createpc', 'updatepc', 'import-excel-orders14', 'createf', 'updatef' ],
                        'allow' => true,
                        'roles' => ['Administrator', 'Production'],
                    ],
                    [
                        'actions' => ['index', 'view',
                            'indexpc', 'arrive', 'plantingm', 'plantingf', 'transplantingm', 'transplantingf', 'cp', 'onlypc', 'harvest', 'clean', 'finish', 'historial', 'orderhistory', 'canceled'
                        ],
                        'allow' => true,
                        'roles' => ['Viewer', 'Administrator', 'Production'],
                    ],
                    [
                        'actions' => ['index', 'view', 'indexpc', 'viewpc', 'cp', 'onlypc', 'harvest', 'clean', 'finish', 'historial', 'orderhistory', 'canceled'],
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
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['=', 'hybrid.delete', 0])
            ->andFilterWhere(['>', 'order.steamDesinfectionU', date('Y-m-d')])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->andFilterWhere(['!=', 'order.sowingDateF', '1970-01-01'])
            ->andFilterWhere(['=', 'order.trial_id', 1])
            ->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Lists all Pollen collect.
     * @return mixed
     */
    public function actionIndexpc()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['=', 'hybrid.delete',0])
            ->andFilterWhere(['>', 'order.steamDesinfectionU', date('Y-m-d')])
            ->andFilterWhere(['is', 'order.sowingDateF', new \yii\db\Expression('null')])
            ->all();



        return $this->render('pollenCollect/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCanceled()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['=', 'hybrid.delete',0])
            ->andFilterWhere(['!=', 'order.state', 'Active'])
            ->all();

        return $this->render('cancel', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPlantm()
    {
        $searchModel = new OrderSearchm();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('plantM', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
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
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $this->layout = "main2";
        $model = new Order();

        if ($model->load(Yii::$app->request->post())) {

            $idnumHC = $model->compartment_idCompartment;
            $model->compartment_idCompartment = explode(",",$model->compartment_idCompartment)[0];
            echo $model->compartment_idCompartment;
            $model->numCrop = explode(",",$idnumHC)[1];

            $model->hybridIdHybr->variety;
            $model->hybridIdHybr->fatherIdFather->variety;
            $model->hybridIdHybr->motherIdMother->variety;

            $model->germinationPOF = $_POST['germinationPOF'];
            $model->germinationPOM = $_POST['germinationPOM'];

            if (!$model->germinationPOM  > 0){
                $model->germinationPOM = $model->hybridIdHybr->motherIdMother->germination;
            }
            if(!$model->germinationPOF > 0){
                $model->germinationPOF  = $model->hybridIdHybr->fatherIdFather->germination;
            }
            $model->gpOrder = $_POST['gpOrder'];
            $gp = $model->gpOrder;
            $kg = $model->orderKg;

            // 1 = no usar padre, 0 = usar padre en $model->prueba
            if($gp && $kg) {
                // Equivalencia de Kilogramos a gramos.
                $g = $kg*1000;
                // Cantidad de plantas Hembras:
                $cph = floor($g/$gp);
                // Cantidad de plantas Macho:
                if(0 == $model->prueba){
                    $cpm = floor($cph/($model->NumOfFPRow/($model->NumOfPlantsPerRow-$model->NumOfFPRow)));
                }else{
                    $cpm = 0;
                }
                // Cantidad de plantas totales:
                $cpt = $cph+$cpm;
                // Cantidad de líneas:
                $cl = floor(($cpt/$model->NumOfPlantsPerRow));
                // Inicializamos las variables para la cantidad final de plantas
                $cphT = 0;
                // Evaluamos si se va a usar el macho y asigna un valor a la cantidad de plantas totales:
                if($model->prueba == 1) {
                    $cphT = floor($cl*$model->NumOfPlantsPerRow);
                }else{
                    $cphT = floor($cl*$model->NumOfFPRow);
                }
                // Sacamos la estimación con respecto a lo que vamos a plantar:
                $estimacionG = (floor($cphT*$gp));
                $estimacionKg = $estimacionG/1000;
            }
            $model->Density = ($model->compartmentIdCompartment->rowsNum * $model->NumOfPlantsPerRow)/$model->compartmentIdCompartment->netSurface;
            $model->NumOfMPRow = $model->NumOfPlantsPerRow - $model->NumOfFPRow;
            if ($model->prueba == 0) {
                $model->FMRatio = $model->NumOfFPRow / $model->NumOfMPRow;
            }

            $model->plantingDistance = 50;
            if ($model->germinationPOM  > 0){
                $germinationM = $model->germinationPOM;
            }else{
                $germinationM = $model->hybridIdHybr->motherIdMother->germination;
            }
            if($model->germinationPOF > 0){
                $germinationF = $model->germinationPOF;
            }else{
                $germinationF = $model->hybridIdHybr->fatherIdFather->germination;
            }

            $model->numRows = $cl;
            if ($model->numRowsOpt != null){
                $model->numRows = $model->numRowsOpt;
            }

            $ratio = ($model->NumOfFPRow/($model->NumOfPlantsPerRow-$model->NumOfFPRow));
            $model->netNumOfPlantsF = round((((3775/$model->plantingDistance)*$ratio)/(1+$ratio))*$model->numRows);
            $model->netNumOfPlantsM = round((((3775/$model->plantingDistance))/(1+$ratio))*$model->numRows);
            $model->sowingF = ($model->netNumOfPlantsF/$germinationF)*100;
            $model->sowingM = ($model->netNumOfPlantsM/$germinationM)*100;

            $model->sowingF = round($model->sowingF);
            $model->sowingM = round($model->sowingM);
            $model->nurseryF = round(($model->sowingF) * 1.15);
            $model->nurseryM = round(($model->sowingM) * 1.15);
            if ($model->hybridIdHybr->motherIdMother->steril == 50) {
                $model->sowingF = ($model->sowingF) * 2;
                $model->nurseryF = ($model->nurseryF) * 2;
            }
            if ($model->hybridIdHybr->fatherIdFather->steril == 50) {
                $model->nurseryM = ($model->nurseryM) * 2;
                $model->sowingM = ($model->sowingM) * 2;
            }
            $model->calculatedYield = ($model->netNumOfPlantsF*$model->gpOrder)/1000;

            $connection = Yii::$app->getDb();
            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
            $query = $command->queryAll();
            $actualcrop = ArrayHelper::getValue($query, '0');
            $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
            if(!isset($actualcrop)){
                $actualcrop = 1;
            }

            $hola = NumcropHasCompartment::find()->andFilterWhere(['=', 'compartment_idCompartment', $model->compartmentIdCompartment->compNum])
                ->andFilterWhere(['=', 'numcrop_cropnum', $actualcrop])
                ->andFilterWhere(['=' ,'crop_idcrops',1])
                ->all();

            foreach ($hola AS $crop){
                $actualcrop = $actualcrop -1;
            }
// Si el número del crop anterior no es 0, utiliza la fecha del crop actual, si el número de crop anterior es 0 utiliza el actual.

            $lastCrop = NumcropHasCompartment::find()->where('(numcrop_cropnum = :crop) AND compartment_idCompartment = :comp', ['crop' =>  ($actualcrop), 'comp' => $model->compartment_idCompartment])->all();
            $varExtra = 0;
            if(0 == $model->prueba) {
                if ($model->sowingDateM == null || $model->sowingDateM === null) {
                    if (($actualcrop - 1) === 0) {
                        foreach ($lastCrop AS $item) {
                            $model->sowingDateM = date('Y-m-d', strtotime("$item->freeDate - " . (($model->hybridIdHybr->cropIdcrops->transplantingMale) - 1) . " day"));
                        }
                        $varExtra = 1;
                    }
                    if (
                        ($crop = NumcropHasCompartment::find()->where('(numcrop_cropnum = :crop) AND compartment_idCompartment = :comp', ['crop' => ($actualcrop) - 1, 'comp' => $model->compartment_idCompartment])->all())
                        &&
                        ($varExtra == 0)
                    ) {
                        foreach ($crop AS $item) {
                            $model->sowingDateM = date('Y-m-d', strtotime("$item->freeDate - 6 day"));
                        }
                    }
                }
                $model->ReqDeliveryDate = date('Y-m-d', strtotime($model->ReqDeliveryDate));
                $model->orderDate = date('Y-m-d', strtotime($model->orderDate));
                $model->ssRecDate = date('Y-m-d', strtotime($model->ssRecDate));
                $model->sowingDateM = date('Y-m-d', strtotime($model->sowingDateM));
                $mes = date('n', strtotime($model->sowingDateM));
                $dia = date('j', strtotime($model->sowingDateM));
                $model->compartmentIdCompartment->compNum;
                $F1 = $model->hybridIdHybr->cropIdcrops->sowingFemale;
                $TM = $model->hybridIdHybr->cropIdcrops->transplantingMale;
                $TF = $model->hybridIdHybr->cropIdcrops->transplantingFemale;
                $PF = $model->hybridIdHybr->cropIdcrops->pollinitionF;
                $PU = $model->hybridIdHybr->cropIdcrops->pollinitionU;
                $HF = $model->hybridIdHybr->cropIdcrops->harvestF;
                $HU = $model->hybridIdHybr->cropIdcrops->harvestU;
                $SDA = $model->hybridIdHybr->cropIdcrops->steamDesinfection;

                if ($F1 + $model->hybridIdHybr->sowingFemale >= 0) {
                    $model->sowingDateF = date('Y-m-d', strtotime("$model->sowingDateM + " . ($F1 + $model->hybridIdHybr->sowingFemale) . " day"));
                } else {
                    $model->sowingDateF = date('Y-m-d', strtotime("$model->sowingDateM " . ($F1 + $model->hybridIdHybr->sowingFemale) . " day"));
                }

                if ($TM + $model->hybridIdHybr->transplantingMale >= 0) {
                    $model->transplantingM = date('Y-m-d', strtotime("$model->sowingDateM + " . ($TM + $model->hybridIdHybr->transplantingMale) . " day"));
                } else {
                    $model->transplantingM = date('Y-m-d', strtotime("$model->sowingDateM " . ($TM + $model->hybridIdHybr->transplantingMale) . " day"));
                }

                if ($TF + $model->hybridIdHybr->transplantingFemale >= 0) {
                    $model->transplantingF = date('Y-m-d', strtotime("$model->sowingDateF + " . ($TF + $model->hybridIdHybr->transplantingFemale) . " day"));
                } else {
                    $model->transplantingF = date('Y-m-d', strtotime("$model->sowingDateF " . ($TF + $model->hybridIdHybr->transplantingFemale) . " day"));
                }

                if (($mes <= 3)) {
                    $model->transplantingM = date('Y-m-d', strtotime("$model->transplantingM + 7 day"));
                    $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF + 7 day"));
                    if ($mes == 3 && $dia > 10) {
                        $model->transplantingM = date('Y-m-d', strtotime("$model->transplantingM - 7 day"));
                        $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF - 7 day"));
                    }
                } elseif (($mes == 12)) {
                    if ($dia > 10) {
                        $model->transplantingM = date('Y-m-d', strtotime("$model->transplantingM + 7 day"));
                        $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF + 7 day"));
                    }
                }

                if (14 + $model->hybridIdHybr->pollenColectF >= 0) {
                    $model->pollenColectF = date('Y-m-d', strtotime("$model->transplantingM + " . (14 + $model->hybridIdHybr->pollenColectF) . " day"));
                } else {
                    $model->pollenColectF = date('Y-m-d', strtotime("$model->transplantingM " . (14 + $model->hybridIdHybr->pollenColectF) . " day"));
                }

                if (112 + $model->hybridIdHybr->pollenColectU) {
                    $model->pollenColectU = date('Y-m-d', strtotime("$model->pollenColectF + " . (112 + $model->hybridIdHybr->pollenColectU) . " day"));
                } else {
                    $model->pollenColectU = date('Y-m-d', strtotime("$model->pollenColectF " . (112 + $model->hybridIdHybr->pollenColectU) . " day"));
                }

                if ($PF + $model->hybridIdHybr->pollinitionF >= 0) {
                    $model->pollinationF = date('Y-m-d', strtotime("$model->transplantingF + " . ($PF + $model->hybridIdHybr->pollinitionF) . " day"));
                } else {
                    $model->pollinationF = date('Y-m-d', strtotime("$model->transplantingF " . ($PF + $model->hybridIdHybr->pollinitionF) . " day"));
                }

                if ($PU + $model->hybridIdHybr->pollinitionU >= 0) {
                    $model->pollinationU = date('Y-m-d', strtotime("$model->pollinationF + " . ($PU + $model->hybridIdHybr->pollinitionU) . " day"));
                } else {
                    $model->pollinationU = date('Y-m-d', strtotime("$model->pollinationF " . ($PU + $model->hybridIdHybr->pollinitionU) . " day"));
                }

                if ($HF + $model->hybridIdHybr->harvestF >= 0) {
                    $model->harvestF = date('Y-m-d', strtotime("$model->pollinationF + " . ($HF + $model->hybridIdHybr->harvestF) . " day"));
                } else {
                    $model->harvestF = date('Y-m-d', strtotime("$model->pollinationF " . ($HF + $model->hybridIdHybr->harvestF) . " day"));
                }

                if ($HU + $model->hybridIdHybr->harvestU >= 0) {
                    $model->harvestU = date('Y-m-d', strtotime("$model->harvestF + " . ($HU + $model->hybridIdHybr->harvestU) . " day"));
                } else {
                    $model->harvestU = date('Y-m-d', strtotime("$model->harvestF " . ($HU + $model->hybridIdHybr->harvestU) . " day"));
                }
                $model->steamDesinfectionF = $model->harvestU;

                if ($SDA + $model->hybridIdHybr->steamDesinfection >= 0) {
                    $model->steamDesinfectionU = date('Y-m-d', strtotime("$model->steamDesinfectionF + " . ($SDA + $model->hybridIdHybr->steamDesinfection) . " day"));
                } else {
                    $model->steamDesinfectionU = date('Y-m-d', strtotime("$model->steamDesinfectionF " . ($SDA + $model->hybridIdHybr->steamDesinfection) . " day"));
                }

                if (!($model->steamDesinfectionU >= $model->ReqDeliveryDate)) {
                    $model->check = "Great, no problem.";
                } else {
                    $model->check = "Check!";
                }
            }else{
                if ($model->sowingDateF == null || $model->sowingDateF === null) {
                    if (($actualcrop - 1) === 0) {
                        foreach ($lastCrop AS $item) {
                            $model->sowingDateF = date('Y-m-d', strtotime("$item->freeDate - " . (($model->hybridIdHybr->cropIdcrops->transplantingMale) - 1) . " day"));
                        }
                        $varExtra = 1;
                    }
                    if (
                        ($crop = NumcropHasCompartment::find()->where('(numcrop_cropnum = :crop) AND compartment_idCompartment = :comp', ['crop' => ($actualcrop) - 1, 'comp' => $model->compartment_idCompartment])->all())
                        &&
                        ($varExtra == 0)
                    ) {
                        foreach ($crop AS $item) {
                            $model->sowingDateF = date('Y-m-d', strtotime("$item->freeDate + 8 day"));
                        }
                    }
                }

                $model->ReqDeliveryDate = date('Y-m-d', strtotime($model->ReqDeliveryDate));
                $model->orderDate = date('Y-m-d', strtotime($model->orderDate));
                $model->ssRecDate = date('Y-m-d', strtotime($model->ssRecDate));
                $model->sowingDateF = date('Y-m-d', strtotime($model->sowingDateF));
                $mes = date('n', strtotime($model->sowingDateF));
                $dia = date('j', strtotime($model->sowingDateF));
                $model->compartmentIdCompartment->compNum;
                $TF = $model->hybridIdHybr->cropIdcrops->transplantingFemale;
                $HF = $model->hybridIdHybr->cropIdcrops->harvestF;
                $HU = $model->hybridIdHybr->cropIdcrops->harvestU;
                $PF = $model->hybridIdHybr->cropIdcrops->pollinitionF;
                $PU = $model->hybridIdHybr->cropIdcrops->pollinitionU;
                $SDA = $model->hybridIdHybr->cropIdcrops->steamDesinfection;

                if ($TF + $model->hybridIdHybr->transplantingFemale >= 0) {
                    $model->transplantingF = date('Y-m-d', strtotime("$model->sowingDateF + " . ($TF + $model->hybridIdHybr->transplantingFemale) . " day"));
                } else {
                    $model->transplantingF = date('Y-m-d', strtotime("$model->sowingDateF " . ($TF + $model->hybridIdHybr->transplantingFemale) . " day"));
                }

                if (($mes <= 3)) {
                    $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF + 7 day"));
                    if ($mes == 3 && $dia > 10) {
                        $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF - 7 day"));
                    }
                } elseif (($mes == 12)) {
                    if ($dia > 10) {
                        $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF + 7 day"));
                    }
                }

                if ($PF + $model->hybridIdHybr->pollinitionF >= 0) {
                    $model->pollinationF = date('Y-m-d', strtotime("$model->transplantingF + " . ($PF + $model->hybridIdHybr->pollinitionF) . " day"));
                } else {
                    $model->pollinationF = date('Y-m-d', strtotime("$model->transplantingF " . ($PF + $model->hybridIdHybr->pollinitionF) . " day"));
                }

                if ($PU + $model->hybridIdHybr->pollinitionU >= 0) {
                    $model->pollinationU = date('Y-m-d', strtotime("$model->pollinationF + " . ($PU + $model->hybridIdHybr->pollinitionU) . " day"));
                } else {
                    $model->pollinationU = date('Y-m-d', strtotime("$model->pollinationF " . ($PU + $model->hybridIdHybr->pollinitionU) . " day"));
                }

                if ($HF + $model->hybridIdHybr->harvestF >= 0) {
                    $model->harvestF = date('Y-m-d', strtotime("$model->pollinationF + " . ($HF + $model->hybridIdHybr->harvestF) . " day"));
                } else {
                    $model->harvestF = date('Y-m-d', strtotime("$model->pollinationF " . ($HF + $model->hybridIdHybr->harvestF) . " day"));
                }

                if ($HU + $model->hybridIdHybr->harvestU >= 0) {
                    $model->harvestU = date('Y-m-d', strtotime("$model->harvestF + " . ($HU + $model->hybridIdHybr->harvestU) . " day"));
                } else {
                    $model->harvestU = date('Y-m-d', strtotime("$model->harvestF " . ($HU + $model->hybridIdHybr->harvestU) . " day"));
                }
                $model->steamDesinfectionF = $model->harvestU;

                if ($SDA + $model->hybridIdHybr->steamDesinfection >= 0) {
                    $model->steamDesinfectionU = date('Y-m-d', strtotime("$model->steamDesinfectionF + " . ($SDA + $model->hybridIdHybr->steamDesinfection) . " day"));
                } else {
                    $model->steamDesinfectionU = date('Y-m-d', strtotime("$model->steamDesinfectionF " . ($SDA + $model->hybridIdHybr->steamDesinfection) . " day"));
                }

                if (!($model->steamDesinfectionU >= $model->ReqDeliveryDate)) {
                    $model->check = "Great, no problem.";
                } else {
                    $model->check = "Check!";
                }
            }
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
            $query = $command->queryAll();
            $actualcrop = ArrayHelper::getValue($query, '0');
            $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
            if(!isset($actualcrop)){
                $actualcrop = 1;
            }

            $rowsAll = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment AND numcrop_cropnum = :numcomp", [':compartment' => $model->compartment_idCompartment, ':numcomp' => $actualcrop]);
            $queryR = $rowsAll->queryAll();
            $actualrows = ArrayHelper::getValue($queryR, '0');

            $rowsO = ArrayHelper::getValue($actualrows, 'rowsOccupied');
            $rowsL = ArrayHelper::getValue($actualrows, 'rowsLeft');

            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS maxCrop
            FROM numcrop_has_compartment");
            $maxCrop = $command->queryAll();
            $maxCrop = ArrayHelper::getValue($maxCrop, '0');
            $maxCrop = ArrayHelper::getValue($maxCrop, 'maxCrop');

            if($model->numRowsOpt == null || $model->numRowsOpt == 0){
                $model->numRowsOpt = $model->numRows;
            }
            $transaction = \Yii::$app->db->beginTransaction();

            if ($model->save()) {
                $compartment = NumcropHasCompartment::find()
                    ->andFilterWhere(['=', 'numcrop_cropnum', $model->numCrop])
                    ->andFilterWhere(['=', 'compartment_idCompartment', $model->compartment_idCompartment])
                    ->one();

                //$model->numRowsOpt = 2;
                $compartment->rowsLeft = $compartment->rowsLeft - $model->numRowsOpt;
                $compartment->rowsOccupied = $compartment->rowsOccupied + $model->numRowsOpt;
                if (!$compartment->save()){
                    $transaction->rollBack();
                    echo "<script>alert('Error saving Surface Planning')</script>";
                    echo "<script>window.history.back();</script>";
                }

                if($compartment->rowsLeft === 0){
                    $exist = NumcropHasCompartment::find()
                        ->andFilterWhere(['=', 'numcrop_cropnum', ($model->numCrop+1)])
                        ->andFilterWhere(['=', 'compartment_idCompartment', $model->compartment_idCompartment])
                        ->one();
                    if(!$exist){
                        $newNHC = new NumcropHasCompartment();
                        $newNHC->numcrop_cropnum = $model->numCrop+1;
                        $newNHC->compartment_idCompartment = $model->compartment_idCompartment;
                        $newNHC->rowsOccupied = 0;
                        $newNHC->rowsLeft = $model->compartmentIdCompartment->rowsNum;
                        $newNHC->crop_idcrops = 1;
                        $newNHC->estado = "Activo";
                        if(!$newNHC->save()){
                            $transaction->rollBack();
                            echo "<script>alert('Error saving Surface Planning')</script>";
                            echo "<script>window.history.back();</script>";
                        }
                    }
                }

                echo "<script>window.history.back();</script>";
                $transaction->commit();

            } else {
                $transaction->rollBack();
                return $this->renderAjax('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionCreatef()
    {

        $this->layout = "main2";
        $model = new Order();

        if ($model->load(Yii::$app->request->post())) {

            $model->hybridIdHybr->variety;
            $model->hybridIdHybr->fatherIdFather->variety;
            $model->hybridIdHybr->motherIdMother->variety;

            $model->germinationPOF = $_POST['germinationPOF'];

            $model->germinationPOM = $_POST['germinationPOM'];

            if (!$model->germinationPOM  > 0){
                $model->germinationPOM = $model->hybridIdHybr->motherIdMother->germination;
            }
            if(!$model->germinationPOF > 0){
                $model->germinationPOF  = $model->hybridIdHybr->fatherIdFather->germination;
            }
            $model->gpOrder = $_POST['gpOrder'];

            if (
                $model->realisedNrOfPlantsF > 0 && $model->extractedPlantsF > 0
            ) {
                $model->remainingPlantsF = $model->realisedNrOfPlantsF - $model->extractedPlantsF;
            }
            if (
                $model->realisedNrOfPlantsM > 0 && $model->extractedPlantsM > 0
            ) {
                $model->remainingPlantsM = $model->realisedNrOfPlantsM - $model->extractedPlantsM;
            }

            $model->numRows = $model->numRowsOpt;

            $connection = Yii::$app->getDb();
            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
            $query = $command->queryAll();
            $actualcrop = ArrayHelper::getValue($query, '0');
            $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
            if(!isset($actualcrop)){
                $actualcrop = 1;
            }

            $hola = NumcropHasCompartment::find()->andFilterWhere(['=', 'compartment_idCompartment', $model->compartmentIdCompartment->compNum])
                ->andFilterWhere(['=', 'numcrop_cropnum', $actualcrop])
                ->andFilterWhere(['=' ,'crop_idcrops',1])
                ->all();

            foreach ($hola AS $crop){
                $actualcrop = $actualcrop -1;
            }
// Si el número del crop anterior no es 0, utiliza la fecha del crop actual, si el número de crop anterior es 0 utiliza el actual.



            $model->ReqDeliveryDate = date('Y-m-d', strtotime($model->ReqDeliveryDate));
            $model->orderDate = date('Y-m-d', strtotime($model->orderDate));
            $model->ssRecDate = date('Y-m-d', strtotime($model->ssRecDate));
            $model->sowingDateM = date('Y-m-d', strtotime($model->sowingDateM));
            $model->sowingDateF = date('Y-m-d', strtotime($model->sowingDateF));
            $model->transplantingM = date('Y-m-d', strtotime($model->transplantingM));
            $model->transplantingF = date('Y-m-d', strtotime($model->transplantingF));
            $model->pollenColectF = date('Y-m-d', strtotime($model->pollenColectF));
            $model->pollenColectU = date('Y-m-d', strtotime($model->pollenColectU));
            $model->pollinationF = date('Y-m-d', strtotime($model->pollinationF));
            $model->pollinationU = date('Y-m-d', strtotime($model->pollinationU));
            $model->harvestF = date('Y-m-d', strtotime($model->harvestF));
            $model->harvestU = date('Y-m-d', strtotime($model->harvestU));
            $model->steamDesinfectionF = date('Y-m-d', strtotime($model->steamDesinfectionF));
            $model->steamDesinfectionU = date('Y-m-d', strtotime($model->steamDesinfectionU));

            if (!$model->save()) {
                print_r($model->getErrors());
                die;
            }

            echo "<script>window.history.back();</script>";
        } else {
            return $this->renderAjax('createf', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdatef($id)
    {
        $this->layout = "main2";
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->hybridIdHybr->variety;
            $model->hybridIdHybr->fatherIdFather->variety;
            $model->hybridIdHybr->motherIdMother->variety;

            $model->germinationPOF = $_POST['germinationPOF'];

            $model->germinationPOM = $_POST['germinationPOM'];

            if (!$model->germinationPOM  > 0){
                $model->germinationPOM = $model->hybridIdHybr->motherIdMother->germination;
            }
            if(!$model->germinationPOF > 0){
                $model->germinationPOF  = $model->hybridIdHybr->fatherIdFather->germination;
            }
            $model->gpOrder = $_POST['gpOrder'];
            $ratio = $model->FMRatio;
            $germinationM = $model->germinationPOM;
            $germinationF = $model->germinationPOF;

            $model->netNumOfPlantsF = round((((3775/$model->plantingDistance)*$ratio)/(1+$ratio))*$model->numRows);
            $model->netNumOfPlantsM = round((((3775/$model->plantingDistance))/(1+$ratio))*$model->numRows);

            $model->sowingF = ($model->netNumOfPlantsF/$germinationF)*100;
            $model->sowingM = ($model->netNumOfPlantsM/$germinationM)*100;

            $model->sowingF = round($model->sowingF);
            $model->sowingM = round($model->sowingM);
            $model->nurseryF = round(($model->sowingF) * 1.15);
            $model->nurseryM = round(($model->sowingM) * 1.15);
            if ($model->hybridIdHybr->motherIdMother->steril == 50) {
                $model->sowingF = ($model->sowingF) * 2;
                $model->nurseryF = ($model->nurseryF) * 2;
            }
            if ($model->hybridIdHybr->fatherIdFather->steril == 50) {
                $model->nurseryM = ($model->nurseryM) * 2;
                $model->sowingM = ($model->sowingM) * 2;
            }

            if (
                $model->realisedNrOfPlantsF > 0 && $model->extractedPlantsF > 0
            ) {
                $model->remainingPlantsF = $model->realisedNrOfPlantsF - $model->extractedPlantsF;
            }
            if (
                $model->realisedNrOfPlantsM > 0 && $model->extractedPlantsM > 0
            ) {
                $model->remainingPlantsM = $model->realisedNrOfPlantsM - $model->extractedPlantsM;
            }

            $model->numRows = $model->numRowsOpt;

            $connection = Yii::$app->getDb();
            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
            $query = $command->queryAll();
            $actualcrop = ArrayHelper::getValue($query, '0');
            $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
            if(!isset($actualcrop)){
                $actualcrop = 1;
            }

            $hola = NumcropHasCompartment::find()->andFilterWhere(['=', 'compartment_idCompartment', $model->compartmentIdCompartment->compNum])
                ->andFilterWhere(['=', 'numcrop_cropnum', $actualcrop])
                ->andFilterWhere(['=' ,'crop_idcrops',1])
                ->all();

            foreach ($hola AS $crop){
                $actualcrop = $actualcrop -1;
            }
// Si el número del crop anterior no es 0, utiliza la fecha del crop actual, si el número de crop anterior es 0 utiliza el actual.


            $model->ReqDeliveryDate = date('Y-m-d', strtotime($model->ReqDeliveryDate));
            $model->orderDate = date('Y-m-d', strtotime($model->orderDate));
            $model->ssRecDate = date('Y-m-d', strtotime($model->ssRecDate));
            $model->sowingDateM = date('Y-m-d', strtotime($model->sowingDateM));
            $model->sowingDateF = date('Y-m-d', strtotime($model->sowingDateF));
            $model->transplantingM = date('Y-m-d', strtotime($model->transplantingM));
            $model->transplantingF = date('Y-m-d', strtotime($model->transplantingF));
            $model->pollenColectF = date('Y-m-d', strtotime($model->pollenColectF));
            $model->pollenColectU = date('Y-m-d', strtotime($model->pollenColectU));
            $model->pollinationF = date('Y-m-d', strtotime($model->pollinationF));
            $model->pollinationU = date('Y-m-d', strtotime($model->pollinationU));
            $model->harvestF = date('Y-m-d', strtotime($model->harvestF));
            $model->harvestU = date('Y-m-d', strtotime($model->harvestU));
            $model->steamDesinfectionF = date('Y-m-d', strtotime($model->steamDesinfectionF));
            $model->steamDesinfectionU = date('Y-m-d', strtotime($model->steamDesinfectionU));


            if (!$model->save()) {
                print_r($model->getErrors());
                die;
            }

            echo "<script>window.history.back();</script>";
        } else {
            return $this->renderAjax('updatef', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new pollen collect model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreatepc()

    {

        $this->layout = "main2";
        $model = new Order();

        if ($model->load(Yii::$app->request->post())) {
            $model->orderKg = 0;
            $model->gpOrder = 1;

            $model->hybridIdHybr->variety;
            $model->hybridIdHybr->fatherIdFather->variety;
            $model->hybridIdHybr->motherIdMother->variety;

            $model->germinationPOF = 0;
            $model->germinationPOM = $_POST['germinationPOM'];

            if (!$model->germinationPOM  > 0){
                $model->germinationPOM = $model->hybridIdHybr->motherIdMother->germination;
            }

            $model->numRows = $model->numRowsOpt;

            $ratio = ($model->NumOfFPRow/($model->NumOfPlantsPerRow-$model->NumOfFPRow));
            $model->plantingDistance = 50;
            $model->netNumOfPlantsM = round((((3775/$model->plantingDistance))/(1+$ratio))*$model->numRows);
            $model->sowingM = ($model->netNumOfPlantsM/$model->germinationPOM)*100;

            $model->sowingM = round($model->sowingM);
            $model->nurseryM = round(($model->netNumOfPlantsM) * 1.15);
            if ($model->hybridIdHybr->fatherIdFather->steril == 50) {
                $model->nurseryM = ($model->nurseryM) * 2;
                $model->sowingM = ($model->sowingM) * 2;
            }
            $model->calculatedYield = ($model->netNumOfPlantsF*$model->gpOrder)/1000;

            $connection = Yii::$app->getDb();
            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
            $query = $command->queryAll();
            $actualcrop = ArrayHelper::getValue($query, '0');
            $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
            if(!isset($actualcrop)){
                $actualcrop = 1;
            }

            $hola = NumcropHasCompartment::find()->andFilterWhere(['=', 'compartment_idCompartment', $model->compartmentIdCompartment->compNum])
                ->andFilterWhere(['=', 'numcrop_cropnum', $actualcrop])
                ->andFilterWhere(['=' ,'crop_idcrops',1])
                ->all();

            foreach ($hola AS $crop){
                $actualcrop = $actualcrop -1;
            }
// Si el número del crop anterior no es 0, utiliza la fecha del crop actual, si el número de crop anterior es 0 utiliza el actual.

            $lastCrop = NumcropHasCompartment::find()->where('(numcrop_cropnum = :crop) AND compartment_idCompartment = :comp', ['crop' =>  ($actualcrop), 'comp' => $model->compartment_idCompartment])->all();
            $varExtra = 0;
                if ($model->sowingDateM == null || $model->sowingDateM === null) {
                    if (($actualcrop - 1) === 0) {
                        foreach ($lastCrop AS $item) {
                            $model->sowingDateM = date('Y-m-d', strtotime("$item->freeDate - " . (($model->hybridIdHybr->cropIdcrops->transplantingMale) - 1) . " day"));
                        }
                        $varExtra = 1;
                    }
                    if (
                        ($crop = NumcropHasCompartment::find()->where('(numcrop_cropnum = :crop) AND compartment_idCompartment = :comp', ['crop' => ($actualcrop) - 1, 'comp' => $model->compartment_idCompartment])->all())
                        &&
                        ($varExtra == 0)
                    ) {
                        foreach ($crop AS $item) {
                            $model->sowingDateM = date('Y-m-d', strtotime("$item->freeDate - 6 day"));
                        }
                    }
                }
                $model->ReqDeliveryDate = date('Y-m-d', strtotime($model->ReqDeliveryDate));
                $model->orderDate = date('Y-m-d', strtotime($model->orderDate));
                $model->ssRecDate = date('Y-m-d', strtotime($model->ssRecDate));
                $model->sowingDateM = date('Y-m-d', strtotime($model->sowingDateM));
                $mes = date('n', strtotime($model->sowingDateM));
                $dia = date('j', strtotime($model->sowingDateM));
                $model->compartmentIdCompartment->compNum;
                $F1 = $model->hybridIdHybr->cropIdcrops->sowingFemale;
                $TM = $model->hybridIdHybr->cropIdcrops->transplantingMale;
                $TF = $model->hybridIdHybr->cropIdcrops->transplantingFemale;
                $PF = $model->hybridIdHybr->cropIdcrops->pollinitionF;
                $PU = $model->hybridIdHybr->cropIdcrops->pollinitionU;
                $HF = $model->hybridIdHybr->cropIdcrops->harvestF;
                $HU = $model->hybridIdHybr->cropIdcrops->harvestU;
                $SDA = $model->hybridIdHybr->cropIdcrops->steamDesinfection;

                if ($TM + $model->hybridIdHybr->transplantingMale >= 0) {
                    $model->transplantingM = date('Y-m-d', strtotime("$model->sowingDateM + " . ($TM + $model->hybridIdHybr->transplantingMale) . " day"));
                } else {
                    $model->transplantingM = date('Y-m-d', strtotime("$model->sowingDateM " . ($TM + $model->hybridIdHybr->transplantingMale) . " day"));
                }

                if (($mes <= 3)) {
                    $model->transplantingM = date('Y-m-d', strtotime("$model->transplantingM + 7 day"));
                    if ($mes == 3 && $dia > 10) {
                        $model->transplantingM = date('Y-m-d', strtotime("$model->transplantingM - 7 day"));
                    }
                } elseif (($mes == 12)) {
                    if ($dia > 10) {
                        $model->transplantingM = date('Y-m-d', strtotime("$model->transplantingM + 7 day"));
                    }
                }

                if (14 + $model->hybridIdHybr->pollenColectF >= 0) {
                    $model->pollenColectF = date('Y-m-d', strtotime("$model->transplantingM + " . (14 + $model->hybridIdHybr->pollenColectF) . " day"));
                } else {
                    $model->pollenColectF = date('Y-m-d', strtotime("$model->transplantingM " . (14 + $model->hybridIdHybr->pollenColectF) . " day"));
                }

                if (112 + $model->hybridIdHybr->pollenColectU) {
                    $model->pollenColectU = date('Y-m-d', strtotime("$model->pollenColectF + " . (112 + $model->hybridIdHybr->pollenColectU) . " day"));
                } else {
                    $model->pollenColectU = date('Y-m-d', strtotime("$model->pollenColectF " . (112 + $model->hybridIdHybr->pollenColectU) . " day"));
                }

                $model->steamDesinfectionF = $model->pollenColectU ;

                if ($SDA + $model->hybridIdHybr->steamDesinfection >= 0) {
                    $model->steamDesinfectionU = date('Y-m-d', strtotime("$model->steamDesinfectionF + " . ($SDA + $model->hybridIdHybr->steamDesinfection) . " day"));
                } else {
                    $model->steamDesinfectionU = date('Y-m-d', strtotime("$model->steamDesinfectionF " . ($SDA + $model->hybridIdHybr->steamDesinfection) . " day"));
                }

                if (!($model->steamDesinfectionU >= $model->ReqDeliveryDate)) {
                    $model->check = "Great, no problem.";
                } else {
                    $model->check = "Check!";
                }
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
            $query = $command->queryAll();
            $actualcrop = ArrayHelper::getValue($query, '0');
            $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
            if(!isset($actualcrop)){
                $actualcrop = 1;
            }
            $model->numCrop = $actualcrop;

            $rowsAll = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment AND numcrop_cropnum = :numcomp", [':compartment' => $model->compartment_idCompartment, ':numcomp' => $actualcrop]);
            $queryR = $rowsAll->queryAll();
            $actualrows = ArrayHelper::getValue($queryR, '0');

            $rowsO = ArrayHelper::getValue($actualrows, 'rowsOccupied');
            $rowsL = ArrayHelper::getValue($actualrows, 'rowsLeft');

            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS maxCrop
            FROM numcrop_has_compartment");
            $maxCrop = $command->queryAll();
            $maxCrop = ArrayHelper::getValue($maxCrop, '0');
            $maxCrop = ArrayHelper::getValue($maxCrop, 'maxCrop');


            if($model->numRowsOpt == null || $model->numRowsOpt == 0){
                $model->numRowsOpt = $model->numRows;
            }

            if ($model->save()) {

                if(isset($maxCrop)){
                    if(isset($actualcrop)){
                        if(0 == ($rowsL - $model->numRows)){
                            if(isset($rowsL)){
                                $actualcrop = $actualcrop +1;
                                if($actualcrop > $maxCrop){
                                    $modelNum = new Numcrop();
                                    $modelNum->save();
                                }
                                $model->numCrop = $actualcrop-1;
                            }
                            $modelNC = new NumcropHasCompartment();
                            $modelNC->createDate = date('Y-m-d');
                            $modelNC->rowsOccupied = 0;
                            $modelNC->rowsLeft = ($model->compartmentIdCompartment->rowsNum);
                            $modelNC->compartment_idCompartment = $model->compartment_idCompartment;
                            $modelNC->numcrop_cropnum = $actualcrop;
                            $modelNC->crop_idcrops = 1;


                            $new = NumcropHasCompartment::findOne([
                                'numcrop_cropnum' => $modelNC->numcrop_cropnum,
                                'compartment_idCompartment' => $modelNC->compartment_idCompartment])->isNewRecord;


                            if ($new){
                                $modelNC->save();
                            }

                            $modelOld = NumcropHasCompartment::findOne(['numcrop_cropnum' => ($actualcrop-1), 'compartment_idCompartment' => $model->compartment_idCompartment]);
                            $modelOld->rowsLeft = new \stdClass();
                            $modelOld->rowsLeft = 0;
                            $modelOld->rowsOccupied = $model->compartmentIdCompartment->rowsNum;

                            $modelOld->save();
                            print_r($modelOld->getErrors());
                            print_r($modelNC->getErrors());

                        }else{
                            $has = NumcropHasCompartment::findOne(['numcrop_cropnum' => $actualcrop, 'compartment_idCompartment' => $model->compartment_idCompartment]);
                            if($has) {
                                $has->rowsOccupied = $has->rowsOccupied + $model->numRows;
                                $has->rowsLeft = $has->rowsLeft - $model->numRows;
                                $orders = Order::find()
                                    ->andFilterWhere(['numcrop' => $actualcrop])
                                    ->andFilterWhere(['compartment_idCompartment' => $model->compartment_idCompartment])
                                    ->max('steamDesinfectionU');
                                ;
                                if($orders) {
                                    if ($model->steamDesinfectionU > $orders) {
                                        $has->freeDate = $model->steamDesinfectionU;
                                    } else {
                                        $has->freeDate = $orders;
                                    }
                                }else{
                                    $has->freeDate = $model->steamDesinfectionU;
                                }
                                $has->save();
                            }
                        }
                    }
                }else{
                    $modelN = new Numcrop();
                    $modelN->save();
                }

                echo "<script>window.history.back();</script>";
            } else {
                print_r($model->getErrors());
                die;
                return $this->renderAjax('pollenCollect/create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->renderAjax('pollenCollect/create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * See historial of the variaty.
     * @return mixed
     */
    public function actionHistorial()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['=', 'order.delete',0])
            ->all();

        return $this->render('history');
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $name=0)
    {
        $this->layout = "main2";

        $model = $this->findModel($id);
        $models = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if ($model->compartment_idCompartment == "same"){
                $model->compartment_idCompartment = $models->compartment_idCompartment.",".$models->numCrop;
            }
            $idnumHC = $model->compartment_idCompartment;
            $model->compartment_idCompartment = explode(",",$model->compartment_idCompartment)[0];
//            echo $model->compartment_idCompartment;
            $model->numCrop = explode(",",$idnumHC)[1];

            $model->germinationPOF = $_POST['germinationPOF'];
            $model->germinationPOM = $_POST['germinationPOM'];

            $hecho = new Facil();
            $hecho->editar($model, $models);
//            $model->save();
  //          $model = Order::findOne(['idorder' => $model->idorder]);

            // Editar todos los eventos.w
            // Stock Seeds Recieved Date - Recieved Seeds

            if ($model->state == "Canceled") {
                $model->canceledDate = date('Y-m-d');
            } else {
                $model->canceledDate = null;
            }


            if (!$model->germinationPOM > 0) {
                $model->germinationPOM = $model->hybridIdHybr->motherIdMother->germination;
            }
            if (!$model->germinationPOF > 0) {
                $model->germinationPOF = $model->hybridIdHybr->fatherIdFather->germination;
            }
            $gp = $model->gpOrder;
            $kg = $model->orderKg;


            if ($gp && $kg) {

                // Equivalencia de Kilogramos a gramos.
                $g = $kg * 1000;
                // Cantidad de plantas Hembras:
                $cph = floor($g / $gp);
                // Cantidad de plantas Macho:
                if (0 == $model->prueba) {
                    $cpm = floor($cph / ($model->NumOfFPRow/($model->NumOfPlantsPerRow-$model->NumOfFPRow)));
                } else {
                    $cpm = 0;
                }
                // Cantidad de plantas totales:
                $cpt = $cph + $cpm;
                // Cantidad de líneas:
                $cl = floor(($cpt / $model->NumOfPlantsPerRow));
                // Inicializamos las variables para la cantidad final de plantas
                $cphT = 0;
                // Evaluamos si se va a usar el macho y asigna un valor a la cantidad de plantas totales:
                if ($model->prueba == 1) {
                    $cphT = floor($cl * $model->NumOfPlantsPerRow);
                } else {
                    // Evaluamos si la cantidad de líneas es mayor a 4:
                    $cphT = floor($cl * $model->NumOfFPRow);
                }
                $model->Density = ($model->compartmentIdCompartment->rowsNum * $model->NumOfPlantsPerRow)/$model->compartmentIdCompartment->netSurface;
                $model->NumOfMPRow = $model->NumOfPlantsPerRow - $model->NumOfFPRow;
                if ($model->prueba == 0) {
                    $model->FMRatio = $model->NumOfFPRow / $model->NumOfMPRow;
                }
                // Sacamos la estimación con respecto a lo que vamos a plantar:
                $estimacionG = (floor($cphT * $gp));
                $estimacionKg = $estimacionG / 1000;
            }



            if (!$model->germinationPOM  > 0){
                $model->germinationPOM = $model->hybridIdHybr->motherIdMother->germination;
            }
            if(!$model->germinationPOF > 0){
                $model->germinationPOF  = $model->hybridIdHybr->fatherIdFather->germination;
            }
            $model->gpOrder = $_POST['gpOrder'];
            $ratio = $model->FMRatio;
            $germinationM = $model->germinationPOM;
            $germinationF = $model->germinationPOF;

            $model->netNumOfPlantsF = round((((3775/$model->plantingDistance)*$ratio)/(1+$ratio))*$model->numRows);
            $model->netNumOfPlantsM = round((((3775/$model->plantingDistance))/(1+$ratio))*$model->numRows);

            $model->sowingF = ($model->netNumOfPlantsF/$germinationF)*100;
            $model->sowingM = ($model->netNumOfPlantsM/$germinationM)*100;

            $model->sowingF = round($model->sowingF);
            $model->sowingM = round($model->sowingM);
            $model->nurseryF = round(($model->sowingF) * 1.15);
            $model->nurseryM = round(($model->sowingM) * 1.15);
            if ($model->hybridIdHybr->motherIdMother->steril == 50) {
                $model->sowingF = ($model->sowingF) * 2;
                $model->nurseryF = ($model->nurseryF) * 2;
            }
            if ($model->hybridIdHybr->fatherIdFather->steril == 50) {
                $model->nurseryM = ($model->nurseryM) * 2;
                $model->sowingM = ($model->sowingM) * 2;
            }

            if (
                $model->realisedNrOfPlantsF > 0 && $model->extractedPlantsF > 0
            ) {
                $model->remainingPlantsF = $model->realisedNrOfPlantsF - $model->extractedPlantsF;
            }
            if (
                $model->realisedNrOfPlantsM > 0 && $model->extractedPlantsM > 0
            ) {
                $model->remainingPlantsM = $model->realisedNrOfPlantsM - $model->extractedPlantsM;
            }


            if ($model->prueba == 1) {
                $model->sowingDateM = null;
                $model->transplantingM = null;
                $model->pollenColectF = null;
                $model->pollenColectU = null;
            }


            if (
                $model->realisedNrOfPlantsF > 0 && $model->extractedPlantsF > 0
            ) {
                $model->remainingPlantsF = $model->realisedNrOfPlantsF - $model->extractedPlantsF;
            }
            if (
                $model->realisedNrOfPlantsM > 0 && $model->extractedPlantsM > 0
            ) {
                $model->remainingPlantsM = $model->realisedNrOfPlantsM - $model->extractedPlantsM;
            }


            $connection = Yii::$app->getDb();
            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
            $query = $command->queryAll();
            $actualcrop = ArrayHelper::getValue($query, '0');
            $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
            if(!isset($actualcrop)){
                $actualcrop = 1;
            }

            $rowsAll = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment AND numcrop_cropnum = :numcomp", [':compartment' => $model->compartment_idCompartment, ':numcomp' => $actualcrop]);
            $queryR = $rowsAll->queryAll();
            $actualrows = ArrayHelper::getValue($queryR, '0');

            $rowsO = ArrayHelper::getValue($actualrows, 'rowsOccupied');
            $rowsL = ArrayHelper::getValue($actualrows, 'rowsLeft');

            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS maxCrop
            FROM numcrop_has_compartment");
            $maxCrop = $command->queryAll();
            $maxCrop = ArrayHelper::getValue($maxCrop, '0');
            $maxCrop = ArrayHelper::getValue($maxCrop, 'maxCrop');

            $rows = $model->numRows;

            if ($model->numRowsOpt != null) {
                if($models->numRowsOpt != null){
                    $model->numRows = $model->numRowsOpt - $models->numRowsOpt;
                }else {
                    $model->numRows =  $model->numRowsOpt - $models->numRows;
                }
            }else{
                if($models->numRowsOpt != null){
                    $model->numRows = $models->numRows - $models->numRowsOpt;
                }else{
                    $model->numRows = $model->numRows  - $models->numRows;
                }
            }
            $diferencia = $model->numRows;
            $model->numRows = $rows;

            if($model->numRowsOpt == null || $model->numRowsOpt == 0){
                $model->numRowsOpt = $model->numRows;
            }

            $compartment = NumcropHasCompartment::find()
                ->andFilterWhere(['=', 'numcrop_cropnum', $model->numCrop])
                ->andFilterWhere(['=', 'compartment_idCompartment', $model->compartment_idCompartment])
                ->one();

            $compartments = NumcropHasCompartment::find()
                ->andFilterWhere(['=', 'numcrop_cropnum', $models->numCrop])
                ->andFilterWhere(['=', 'compartment_idCompartment', $models->compartment_idCompartment])
                ->one();

            $transaction = \Yii::$app->db->beginTransaction();
            //$model->numRowsOpt = 2;

            $model->numRows = $model->numRowsOpt;
            if ($models->compartment_idCompartment == $model->compartment_idCompartment && $model->numCrop == $models->numCrop) {
                $compartment->rowsLeft = $compartment->rowsLeft - $diferencia;
                $compartment->rowsOccupied = $compartment->rowsOccupied + $diferencia;
            }else{
                $compartments->rowsLeft = $compartments->rowsLeft + $models->numRowsOpt;
                $compartments->rowsOccupied = $compartments->rowsOccupied - $models->numRowsOpt;

                $compartment->rowsLeft = $compartment->rowsLeft - $model->numRowsOpt;
                $compartment->rowsOccupied = $compartment->rowsOccupied + $model->numRowsOpt;
            }
            if ($compartment->rowsLeft < 0 || $compartments->rowsLeft < 0){
                echo "<script>alert('The difference is: ".$diferencia." which turns the Rows Left in surface planning to ".$compartment->rowsLeft." So your changes are negated.')</script>";
                echo "<script>window.history.back();</script>";
                die;
            }
            if ($model->save()) {

                if (!$compartments->save()){
                    $transaction->rollBack();
                    echo "<script>alert('Error saving Surface Planning')</script>";
                    echo "<script>window.history.back();</script>";
                }

                if (!$compartment->save()){
                    $transaction->rollBack();
                    echo "<script>alert('Error saving Surface Planning')</script>";
                    echo "<script>window.history.back();</script>";
                }

                if($compartment->rowsLeft == 0){
                    $exist = NumcropHasCompartment::find()
                        ->andFilterWhere(['=', 'numcrop_cropnum', ($model->numCrop+1)])
                        ->andFilterWhere(['=', 'compartment_idCompartment', $model->compartment_idCompartment])
                        ->one();
                    if(!$exist){
                        $newNHC = new NumcropHasCompartment();
                        $newNHC->numcrop_cropnum = $model->numCrop+1;
                        $newNHC->compartment_idCompartment = $model->compartment_idCompartment;
                        $newNHC->rowsOccupied = 0;
                        $newNHC->rowsLeft = $model->compartmentIdCompartment->rowsNum;
                        $newNHC->crop_idcrops = 1;
                        $newNHC->estado = "Activo";
                        if(!$newNHC->save()){
                            $transaction->rollBack();
                            echo "<script>alert('Error saving Surface Planning')</script>";
                            echo "<script>window.history.back();</script>";
                        }
                    }
                }

                $transaction->commit();
                echo "<script>window.history.back();</script>";
                die;
            }else{
                $transaction->rollBack();
                return $this->renderAjax('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }


    public function actionUpdates($id)
    {

        $order = Order::findOne($id);


        if ($order->load(Yii::$app->request->post())) {
            if ($order->selector == "Active"){
                $order->selector = "Inactive";
            }else{
                $order->selector = "Active";
            }
            $order->rfselectorc = $order->rfselectorc." Changed by: ".Yii::$app->user->identity->username.".";
            $order->save();
            echo "<script>window.history.back();</script>";
            die;
        } else {
            return $this->renderAjax('seedsprocess/finish/create', [
                'model' => $order,
            ]);
        }
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdatepc($id, $name=0)
    {
        $this->layout = "main2";

        $model = $this->findModel($id);
        $models = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $hecho = new Facil();
            $hecho->editarpc($model, $models);
            $model->save();
            $model = Order::findOne(['idorder' => $model->idorder]);

            // Editar todos los eventos.w
            // Stock Seeds Recieved Date - Recieved Seeds

            if ($model->state == "Canceled") {
                $model->canceledDate = date('Y-m-d');
            } else {
                $model->canceledDate = null;
            }


            if (!$model->germinationPOM > 0) {
                $model->germinationPOM = $model->hybridIdHybr->motherIdMother->germination;
            }

            if (
                $model->realisedNrOfPlantsM > 0 && $model->extractedPlantsM > 0
            ) {
                $model->remainingPlantsM = $model->realisedNrOfPlantsM - $model->extractedPlantsM;
            }


            $connection = Yii::$app->getDb();
            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
            $query = $command->queryAll();
            $actualcrop = ArrayHelper::getValue($query, '0');
            $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
            if(!isset($actualcrop)){
                $actualcrop = 1;
            }
            $model->numCrop = $actualcrop;


            $rowsAll = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment AND numcrop_cropnum = :numcomp", [':compartment' => $model->compartment_idCompartment, ':numcomp' => $actualcrop]);
            $queryR = $rowsAll->queryAll();
            $actualrows = ArrayHelper::getValue($queryR, '0');

            $rowsO = ArrayHelper::getValue($actualrows, 'rowsOccupied');
            $rowsL = ArrayHelper::getValue($actualrows, 'rowsLeft');

            $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS maxCrop
            FROM numcrop_has_compartment");
            $maxCrop = $command->queryAll();
            $maxCrop = ArrayHelper::getValue($maxCrop, '0');
            $maxCrop = ArrayHelper::getValue($maxCrop, 'maxCrop');

            $rows = $model->numRows;
            if ($model->numRowsOpt != null) {
                if($models->numRowsOpt != null){
                    $model->numRows = $model->numRowsOpt - $models->numRowsOpt;
                }else {
                    $model->numRows =  $model->numRowsOpt - $models->numRows;
                }
            }else{
                if($models->numRowsOpt != null){
                    $model->numRows = $models->numRows - $models->numRowsOpt;
                }else{
                    $model->numRows = $model->numRows  - $models->numRows;
                }
            }
            $diferencia = $model->numRows;
            $model->numRows = $rows;


            if($model->numRowsOpt == null || $model->numRowsOpt == 0){
                $model->numRowsOpt = $model->numRows;
            }else{
                $model->numRows = $model->numRowsOpt;
            }

            if ($model->save() && $model->hybridIdHybr->cropIdcrops->save()) {

                if(isset($maxCrop)){
                    if(isset($actualcrop)){
                        if(0 == ($rowsL - $model->numRows)){
                            if(isset($rowsL)){
                                $actualcrop = $actualcrop +1;
                                if($actualcrop > $maxCrop){
                                    $modelNum = new Numcrop();
                                    $modelNum->save();
                                }
//                                $model->numCrop = $actualcrop-1;
                            }
                            $modelNC = new NumcropHasCompartment();
                            $modelNC->createDate = date('Y-m-d');
                            $modelNC->rowsOccupied = 0;
                            $modelNC->rowsLeft = ($model->compartmentIdCompartment->rowsNum);
                            $modelNC->compartment_idCompartment = $model->compartment_idCompartment;
                            $modelNC->numcrop_cropnum = $actualcrop;
                            $modelNC->crop_idcrops = 1;


                            $new = NumcropHasCompartment::findOne([
                                'numcrop_cropnum' => $modelNC->numcrop_cropnum,
                                'compartment_idCompartment' => $modelNC->compartment_idCompartment])->isNewRecord;


                            if ($new){
                                $modelNC->save();
                            }

                            $modelOld = NumcropHasCompartment::findOne(['numcrop_cropnum' => ($actualcrop-1), 'compartment_idCompartment' => $model->compartment_idCompartment]);
                            $modelOld->rowsLeft = new \stdClass();
                            $modelOld->rowsLeft = 0;
                            $modelOld->rowsOccupied = $model->compartmentIdCompartment->rowsNum;

                            $modelOld->save();
                            print_r($modelOld->getErrors());
                            print_r($modelNC->getErrors());

                        }else{
                            $has = NumcropHasCompartment::findOne(['numcrop_cropnum' => $model->numCrop, 'compartment_idCompartment' => $model->compartment_idCompartment]);
                            if($has && $model->compartment_idCompartment == $models->compartment_idCompartment) {
                                $has->rowsOccupied = $has->rowsOccupied + $diferencia;
                                $has->rowsLeft = $has->rowsLeft - $diferencia;
                                $orders = Order::find()
                                    ->andFilterWhere(['numcrop' => $model->numCrop])
                                    ->andFilterWhere(['compartment_idCompartment' => $model->compartment_idCompartment])
                                    ->max('steamDesinfectionU');
                                ;
                                if($orders) {
                                    if ($model->steamDesinfectionU > $orders) {
                                        $has->freeDate = $model->steamDesinfectionU;
                                    } else {
                                        $has->freeDate = $orders;
                                    }
                                }else{
                                    $has->freeDate = $model->steamDesinfectionU;
                                }
                                $has->save();
                                if($has->rowsLeft == 0){
                                    $actualcrop = $actualcrop +1;
                                    if($actualcrop > $maxCrop){
                                        $modelNum = new Numcrop();
                                        $modelNum->save();
                                    }
                                    $model->numCrop = $actualcrop-1;

                                    $modelNC = new NumcropHasCompartment();
                                    $modelNC->createDate = date('Y-m-d');
                                    $modelNC->rowsOccupied = 0;
                                    $modelNC->rowsLeft = ($model->compartmentIdCompartment->rowsNum);
                                    $modelNC->compartment_idCompartment = $model->compartment_idCompartment;
                                    $modelNC->numcrop_cropnum = $actualcrop;
                                    $modelNC->crop_idcrops = 1;

                                    $new = NumcropHasCompartment::findOne([
                                        'numcrop_cropnum' => $modelNC->numcrop_cropnum,
                                        'compartment_idCompartment' => $modelNC->compartment_idCompartment])->isNewRecord;


                                    if ($new){
                                        $modelNC->save();
                                    }

                                    $modelOld = NumcropHasCompartment::findOne(['numcrop_cropnum' => ($actualcrop-1), 'compartment_idCompartment' => $model->compartment_idCompartment]);
                                    $modelOld->rowsLeft = new \stdClass();
                                    $modelOld->rowsLeft = 0;
                                    $modelOld->rowsOccupied = $model->compartmentIdCompartment->rowsNum;
                                    $modelOld->save();

                                    $model->save();
                                }
                            }else if($has && $model->compartment_idCompartment != $models->compartment_idCompartment){
                                $has = NumcropHasCompartment::findOne(['numcrop_cropnum' => $actualcrop, 'compartment_idCompartment' => $models->compartment_idCompartment]);
                                if($models->numRowsOpt){
                                    $has->rowsOccupied = $has->rowsOccupied - $models->numRowsOpt;
                                    $has->rowsLeft = $has->rowsLeft + $models->numRowsOpt;
                                }else{
                                    $has->rowsOccupied = $has->rowsOccupied - $models->numRows;
                                    $has->rowsLeft = $has->rowsLeft + $models->numRows;
                                }

                                $newc = NumcropHasCompartment::findOne(['numcrop_cropnum' => $actualcrop, 'compartment_idCompartment' => $model->compartment_idCompartment]);

                                if($model->numRowsOpt){
                                    $newc->rowsOccupied = $newc->rowsOccupied - $model->numRowsOpt;
                                    $newc->rowsLeft = $newc->rowsLeft + $model->numRowsOpt;
                                }else{
                                    $newc->rowsOccupied = $newc->rowsOccupied - $model->numRows;
                                    $newc->rowsLeft = $newc->rowsLeft + $model->numRows;
                                }
                                $orders = Order::find()
                                    ->andFilterWhere(['numcrop' => $actualcrop])
                                    ->andFilterWhere(['compartment_idCompartment' => $model->compartment_idCompartment])
                                    ->max('steamDesinfectionU');
                                ;
                                if($orders) {
                                    if ($model->steamDesinfectionU > $orders) {
                                        $newc->freeDate = $model->steamDesinfectionU;
                                    } else {
                                        $newc->freeDate = $orders;
                                    }
                                }else{
                                    $newc->freeDate = $model->steamDesinfectionU;
                                }
                                $newc->save();
                                if($newc->rowsLeft == 0){
                                    $actualcrop = $actualcrop +1;
                                    if($actualcrop > $maxCrop){
                                        $modelNum = new Numcrop();
                                        $modelNum->save();
                                    }
                                    $model->numCrop = $actualcrop-1;

                                    $modelNC = new NumcropHasCompartment();
                                    $modelNC->createDate = date('Y-m-d');
                                    $modelNC->rowsOccupied = 0;
                                    $modelNC->rowsLeft = ($model->compartmentIdCompartment->rowsNum);
                                    $modelNC->compartment_idCompartment = $model->compartment_idCompartment;
                                    $modelNC->numcrop_cropnum = $actualcrop;
                                    $modelNC->crop_idcrops = 1;

                                    $new = NumcropHasCompartment::findOne([
                                        'numcrop_cropnum' => $modelNC->numcrop_cropnum,
                                        'compartment_idCompartment' => $modelNC->compartment_idCompartment])->isNewRecord;


                                    if ($new){
                                        $modelNC->save();
                                    }

                                    $modelOld = NumcropHasCompartment::findOne(['numcrop_cropnum' => ($actualcrop-1), 'compartment_idCompartment' => $model->compartment_idCompartment]);
                                    $modelOld->rowsLeft = new \stdClass();
                                    $modelOld->rowsLeft = 0;
                                    $modelOld->rowsOccupied = $model->compartmentIdCompartment->rowsNum;
                                    $modelOld->save();

                                    $model->save();
                                }
                            }
                        }
                    }
                }else{
                    $modelN = new Numcrop();
                    $modelN->save();
                }
                if($name === 0){
                    echo "<script>window.history.back();</script>";
                }else{
                    $searchModel1 = new OrderSearch();
                    $dataProvider1 = $searchModel1->search(Yii::$app->request->queryParams);

                    $searchModel1 = OrderSearch::find()->where('(order.state = "Seeds on its way") OR (order.state = "Seeds arrive")')->all();
//                    $order = new OrdersController($this->id);
                    $searchModel = new OrderSearch();
                    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                    $dataProvider->sort = ['defaultOrder' => ['ssRecDate'=>SORT_ASC]];
                    $dataProvider->query
                        ->andFilterWhere(['=', 'order.delete',0]);
//                    $this->render('seedsprocess/'.$name."/index", [
                    //                      'searchModel' => $searchModel,
                    //                    'dataProvider' => $dataProvider,
                    //              ]);

                    $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                    echo "<script>window.history.back();</script>";
//                    header("Location: {$_SERVER['HTTP_REFERER']}");
//                    return $this->redirect($edit);

                }
            }else{
                return $this->renderAjax('pollenCollect/update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->renderAjax('pollenCollect/update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $name=0)
    {
        $model = $this->findModel($id);
        if (($modelNHC = NumcropHasCompartment::findOne(['numcrop_cropnum' => ($model->numCrop), 'compartment_idCompartment' => $model->compartment_idCompartment])) !== null){
            if($modelNHC->rowsLeft == 0){
                $modelNHC->lastUpdatedDate = date("Y-m-d");
                if ($model->numRowsOpt){
                    $modelNHC->rowsOccupied = $modelNHC->rowsOccupied - $model->numRowsOpt;
                    $modelNHC->rowsLeft = $modelNHC->rowsLeft + $model->numRowsOpt;
                }else{
                    $modelNHC->rowsOccupied = $modelNHC->rowsOccupied - $model->numRows;
                    $modelNHC->rowsLeft = $modelNHC->rowsLeft + $model->numRows;
                }
                $modelNHC->save();
            }else{
                $modelNHC->lastUpdatedDate = date("Y-m-d");
                if ($model->numRowsOpt){
                    $modelNHC->rowsOccupied = $modelNHC->rowsOccupied - $model->numRowsOpt;
                    $modelNHC->rowsLeft = $modelNHC->rowsLeft + $model->numRowsOpt;
                }else{
                    $modelNHC->rowsOccupied = $modelNHC->rowsOccupied - $model->numRows;
                    $modelNHC->rowsLeft = $modelNHC->rowsLeft + $model->numRows;
                }
                $modelNHC->save();
            }
        } elseif (($modelNHC = NumcropHasCompartment::findOne(['numcrop_cropnum' => ($model->numCrop)-1, 'compartment_idCompartment' => $model->compartment_idCompartment])) !== null){
            if($modelNHC->rowsLeft == 0){
                $modelNHC->lastUpdatedDate = date("Y-m-d");
                if ($model->numRowsOpt){
                    $modelNHC->rowsOccupied = $modelNHC->rowsOccupied - $model->numRowsOpt;
                    $modelNHC->rowsLeft = $modelNHC->rowsLeft + $model->numRowsOpt;
                }else{
                    $modelNHC->rowsOccupied = $modelNHC->rowsOccupied - $model->numRows;
                    $modelNHC->rowsLeft = $modelNHC->rowsLeft + $model->numRows;
                }
                $modelNHC->save();
            }else{
                $modelNHC->lastUpdatedDate = date("Y-m-d");
                if ($model->numRowsOpt){
                    $modelNHC->rowsOccupied = $modelNHC->rowsOccupied - $model->numRowsOpt;
                    $modelNHC->rowsLeft = $modelNHC->rowsLeft + $model->numRowsOpt;
                }else{
                    $modelNHC->rowsOccupied = $modelNHC->rowsOccupied - $model->numRows;
                    $modelNHC->rowsLeft = $modelNHC->rowsLeft + $model->numRows;
                }
                $modelNHC->save();
            }
        }
        $model->delete = 1;
        $model->save();
        echo "<script>window.history.back();</script>";
    }

    public function actionOrderhistory($columns, $id, $crops, $compartments ){
//            $columns = "c.compNum,h.variety AS Hybrid,m.variety AS Mother,f.variety AS Father,cr.crop,n.numcompartment AS Nursery,o.numCrop,o.orderKg,o.numRows";
//            $id = 1;
        $hybrids = explode(",",$id);
        $compartments = explode(",",$compartments);
        $crops = explode(",",$crops);
        $params = "";
        $compart = "";
        $cropp = "";
        $count = 0;
        foreach($hybrids AS $hybrid){
            $count++;
            if ($count == 1) {
                $params = $params . "AND (h.variety = '$hybrid' ";
            }else{
                $params = $params . "OR h.variety = '$hybrid' ";
            }
        }
        $count = 0;
        foreach($compartments AS $compartment){
            $count++;
            if ($count == 1) {
                $compart = $compart . " AND (c.idCompartment = $compartment ";
            }else{
                $compart = $compart . "OR c.idCompartment = $compartment ";
            }
        }
        $count = 0;
        foreach($crops AS $crop){
            $count++;
            if ($count == 1) {
                $cropp = $cropp . " AND (o.numCrop = $crop ";
            }else{
                $cropp = $cropp. "OR o.numCrop = $crop ";
            }
        }
        $params = $params . ")";
        $compart = $compart. ")";
        $cropp = $cropp . ")";
                $columnSorts = explode(",",$columns);
                $sorts = "";
                $validar = array();
                $validar[0] = '/c.compNum AS /';
                $validar[1] = '/c.rowsNum AS /';
                $validar[2] = '/h.variety AS /';
                $validar[3] = '/m.variety AS /';
                $validar[4] = '/m.steril AS /';
                $validar[5] = '/m.germination AS /';
                $validar[6] = '/m.tsw AS /';
                $validar[7] = '/f.variety AS /';
                $validar[8] = '/f.steril AS /';
                $validar[9] = '/f.germination AS /';
                $validar[10] = '/f.tsw AS /';
                $validar[11] = '/n.numcompartment AS /';
                $validar[12] = '/cr.crop AS /';

                $remplazar = array();
                $remplazar[2] = "";
                $remplazar[3] = "";
                $remplazar[4] = "";
                $remplazar[5] = "";
                $remplazar[6] = "";
                $remplazar[7] = "";
                $remplazar[8] = "";
                $remplazar[9] = "";
                $remplazar[10] = "";
                $remplazar[11] = "";
                $remplazar[12] = "";
                foreach($columnSorts AS $sort){
                    $sort = preg_replace($validar, $remplazar, $sort);
                    $sorts = $sorts.
                        "'".$sort."' => [
                            'asc' =>['".$sort."' => SORT_ASC],
                            'desc' =>['".$sort."' => SORT_DESC],
                            'default' =>['".$sort."' => SORT_DESC],
                            'label' => '".$sort."',
                        ], ";
                }
                explode(",",$sorts);
                $sql = 'SELECT DISTINCT '.$columns.' 
                    FROM `stocklist_has_order` stho  
                    INNER JOIN `order` o
                    INNER JOIN `numcrop` nc
                    INNER JOIN `compartment` c
                    INNER JOIN `hybrid` h
                    INNER JOIN `Mother` m
                    INNER JOIN `Father` f
                    INNER JOIN `Crop` cr
                    INNER JOIN `Nursery` n
                    WHERE
                    stho.order_idorder = o.idorder
                    AND c.idcompartment = o.compartment_idCompartment
                    AND h.idHybrid = o.Hybrid_idHybrid
                    AND h.Mother_idMother = m.idMother
                    AND h.Father_idFather = f.idFather
                    AND h.Crop_idCrops = cr.idcrops
                    AND n.idnursery = o.nursery_idnursery
                    AND o.delete = 0
                    '.$params.$compart.$cropp.";";
        $dataProvider = new SqlDataProvider([
        'db' => Yii::$app->db,
        'sql' => $sql,
        'sort' =>false,
        'pagination' => false,
    ]);

//        echo Html::csrfMetaTags();

        return $this->render('viewh', [
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionHistory($id)
    {
        $facil = New Facil();
        if($id) {
            $countEstimations = Estimations::find()
                ->innerJoinWith(['orderIdorder'])
                ->innerJoinWith(['orderIdorder.hybridIdHybr'])
                ->andFilterWhere(['=', 'hybrid.idHybrid', $id])
                ->andFilterWhere(['>', 'avgGrsPlant', 0])
                ->all();

            $gpavg = 0;
            $estimationQ = 0;
            foreach ($countEstimations AS $countEstimation){
                $estimationQ += 1;
                $gpavg = $gpavg + $countEstimation->avgGrsPlant;
            }

            if ($estimationQ === 0){
                $estimationQ = 1;
            }

            $gpavg = $gpavg/$estimationQ;

            if ($gpavg > 0) {
              //  echo "<option value='" . $gpavg . "'> " . "PROMEX Real, AGP </option>";
            }


//            echo ' <input type="text" id="Hola" name="myText2" value="" selectBoxOptions="';
            $countOrders = Order::find()
                ->where(['Hybrid_idHybrid' => $id])
                ->count();
            $hybrid = Hybrid::findOne(['idHybrid' => $id]);
            if ($countOrders > 0) {
                $date = date('Y-m-d');
                $data = Order::findBySql('SELECT o.sowingDateM, AVG(o.gpOrder) AS gpOrder FROM `order` o WHERE (Hybrid_idHybrid = ' . $id . ' AND o.delete = 0) AND (o.selector = "Active" || o.selector = "Activr")
                                            UNION 
                                            SELECT "Holland", gP as HollandGP FROM mother WHERE idMother = ' . $hybrid->motherIdMother->idMother . ';
                   ')->all();

                if ($data) {
                    foreach ($data AS $dat) {
                        if ($dat->sowingDateM == "Holland") {
                            echo "<option value='".$facil->limitarDecimales($dat->gpOrder)."'>".$facil->limitarDecimales($dat->sowingDateM) . ", g/p </option>";
                        } else {
                            if(isset($dat->gpOrder )) {
                                echo "<option value='".$facil->limitarDecimales($dat->gpOrder)."'> "."PROMEX Choosen, GP </option>";
                            }
                        }
                    }
                }



            } else {
                $data = Mother::findOne(["idMother" => $hybrid->motherIdMother->idMother]);
                if ($data) {
                    echo "<option value='".$facil->limitarDecimales($data->gP)."'> From Holland, g/p </option>";
                }
            }
        }
    }

    public function actionHistoryf($id)
    {
        $facil = New Facil();
        if($id) {
//            echo ' <input type="text" id="Hola" name="myText2" value="" selectBoxOptions="';
            $countOrders = Order::find()
                ->where(['Hybrid_idHybrid' => $id])
                ->count();
            $hybrid = Hybrid::findOne(['idHybrid' => $id]);
            if ($countOrders > 0) {
                $date = date('Y-m-d');
                $data = Order::findBySql('SELECT o.sowingDateM, AVG(o.germinationPOF) AS gpOrder FROM `order` o WHERE (Hybrid_idHybrid = ' . $id . ' AND o.delete = 0) AND (o.selector = "Active" AND o.steamDesinfectionU < "'.$date.'")
                                            UNION 
                                            SELECT "Holland", germination as HollandG FROM mother WHERE idMother = ' . $hybrid->motherIdMother->idMother . ';
                   ')->all();

                if ($data) {
                    foreach ($data AS $dat) {
                        if ($dat->sowingDateM == "Holland") {
                            echo "<option value='".$facil->limitarDecimales($dat->gpOrder)."'>".$facil->limitarDecimales($dat->sowingDateM) . ", gf </option>";
                        } else {
                            if(isset($dat->gpOrder )) {
                                echo "<option value='".$facil->limitarDecimales($dat->gpOrder)."'> '"."PROMEX, gf </option>";
                            }
                        }
                    }
                }
            } else {
                $data = Mother::findOne(["idMother" => $hybrid->motherIdMother->idMother]);
                if ($data) {
                    echo "<option value='".$data->gP."'> From Holland, gf </option>";
                }
            }
        }
    }

    public function actionHistorym($id)
    {
        $facil = New Facil();
        if($id) {
//            echo ' <input type="text" id="Hola" name="myText2" value="" selectBoxOptions="';
            $countOrders = Order::find()
                ->where(['Hybrid_idHybrid' => $id])
                ->count();
            $hybrid = Hybrid::findOne(['idHybrid' => $id]);
            if ($countOrders > 0) {
                $date = date('Y-m-d');
                $data = Order::findBySql('SELECT o.sowingDateM, AVG(o.germinationPOM) AS gpOrder FROM `order` o WHERE (Hybrid_idHybrid = ' . $id . ' AND o.delete = 0) AND (o.selector = "Active" AND o.steamDesinfectionU < "'.$date.'")
                                            UNION 
                                            SELECT "Holland", germination as HollandG FROM father WHERE idFather = ' . $hybrid->fatherIdFather->idFather . ';
                   ')->all();

                if ($data) {
                    foreach ($data AS $dat) {
                        if ($dat->sowingDateM == "Holland") {
                            echo "<option value='".$facil->limitarDecimales($dat->gpOrder)."'>".$facil->limitarDecimales($dat->sowingDateM). ", gm </option>";
                        } else {
                             if(isset($dat->gpOrder )) {
                                 echo "<option value='".$facil->limitarDecimales($dat->gpOrder)."'> '"."PROMEX, gm </option>";
                            }
                        }
                    }
                }
            } else {
                $data = Mother::findOne(["idMother" => $hybrid->fatherIdFather->idFather]);
                if ($data) {
                    echo "<option value='".$data->gP."'> From Holland, gm </option>";
                }
            }
        }
    }
    public function actionOrder()

    {

        $model = new \backend\models\Order();



        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {

                // form inputs are valid, do something here

                return;

            }

        }



        return $this->render('/order', [

            'model' => $model,

        ]);

    }



    public function actionCompartment($gp, $kg, $nump, $numpf, $rows=null, $idc=0, $numc=0, $males, $same=0)
    {
  //      echo "<script>alert('".$nump."');</script>";
        // true = no usar padre, false = usar padre en $males

        // Evalua si los datos fueron pasados exitosamente
        if($gp && $kg) {
            // Equivalencia de Kilogramos a gramos.
            $g = $kg*1000;
            // Cantidad de plantas Hembras:
            $cph = floor($g/$gp);
            // Cantidad de plantas Macho:
            if('false' == $males){
                $cpm = floor($cph/($numpf/($nump-$numpf)));
            }else{
                $cpm = 0;
            }
            // Cantidad de plantas totales:
            $cpt = $cph+$cpm;
//            echo "<script>alert('".$cpt."');</script>";
            // Cantidad de líneas:
            $cl = floor(($cpt/$nump));
            if ($rows!=null || $rows != ''){
                $cl = $rows;
            }
            // Inicializamos las variables para la cantidad final de plantas
            $cphT = 0;
            // Evaluamos si se va a usar el macho y asigna un valor a la cantidad de plantas totales:
            if($males == 'true') {
                $cphT = floor($cl*$nump);
            }else{
                $cphT = floor($cl*$numpf);
            }
            // Sacamos la estimación con respecto a lo que vamos a plantar:
            $estimacionG = (floor($cphT*$gp));
            $estimacionKg = $estimacionG/1000;
            echo "<option value='s'>Select compartment</option>";
            $compartments = NumcropHasCompartment::find()->joinWith(['compartmentIdCompartment'])->where('((numcrop_has_compartment.compartment_idCompartment = compartment.idCompartment) AND (numcrop_has_compartment.rowsLeft >= :row)) AND numcrop_has_compartment.freeDate IS NOT NULL', ['row' =>  $cl])->andFilterWhere(['>', 'crop_idcrops', 1])->orderBy('numcrop_has_compartment.freeDate')->all();
            echo "<option value='0' disabled='true'>Rows you need: ".$cl.", Estimated production: ".$estimacionKg."</option>";
            // Validamos si es creación de orden o actualización dependiendo de los datos enviados:
            if($idc != 0 && $numc != 0){
                // Mostramos el compartimento actual:
                echo "<option value='".$idc."' selected='true'>Actual compartment: ".$numc."</option>";
                // Quitamos al compartimento ya mencionado previamente.
                $compartments = NumcropHasCompartment::find()->joinWith(['compartmentIdCompartment'])->where('((numcrop_has_compartment.compartment_idCompartment = compartment.idCompartment) AND (numcrop_has_compartment.rowsLeft >= :row)) AND compartment.idCompartment != :idc', ['row' =>  $cl,'idc' => $idc])->orderBy('compartment.compNum')->all();;
            }
            foreach ($compartments AS $compartment){
                echo "<option value='".$compartment->compartment_idCompartment.",".$compartment->numcrop_cropnum."'>Compartment: ".$compartment->compartmentIdCompartment->compNum.", Crop: ".$compartment->numcrop_cropnum.", FreeDate: ".$compartment->freeDate.", Rows left: ".$compartment->rowsLeft.".";
            }
            if ($same == 1){
                echo "<option value='same' selected>Same Compartment </option>";
            }
        }
    }

    public function actionCompartmentpc($rows=null, $idc=0, $numc=0)
    {
            echo "<option value='s'>Select compartment</option>";
            $compartments = NumcropHasCompartment::find()->joinWith(['compartmentIdCompartment'])->where('((numcrop_has_compartment.compartment_idCompartment = compartment.idCompartment) AND (numcrop_has_compartment.rowsLeft >= :row)) AND numcrop_has_compartment.freeDate IS NOT NULL', ['row' =>  $rows])->orderBy('numcrop_has_compartment.freeDate')->all();
            echo "<option value='0' disabled='true'>Rows you need: ".$rows."</option>";
            // Validamos si es creación de orden o actualización dependiendo de los datos enviados:
            if($idc != 0 && $numc != 0){
                // Mostramos el compartimento actual:
                echo "<option value='".$idc."' selected='true'>Actual compartment: ".$numc."</option>";
                // Quitamos al compartimento ya mencionado previamente.
                $compartments = NumcropHasCompartment::find()->joinWith(['compartmentIdCompartment'])->where('((numcrop_has_compartment.compartment_idCompartment = compartment.idCompartment) AND (numcrop_has_compartment.rowsLeft >= :row)) AND compartment.idCompartment != :idc', ['row' =>  $rows,'idc' => $idc])->orderBy('compartment.compNum')->all();;
            }
            foreach ($compartments AS $compartment){
                echo "<option value='".$compartment->compartment_idCompartment."'>Compartment: ".$compartment->compartmentIdCompartment->compNum.", Crop: ".$compartment->numcrop_cropnum.", FreeDate: ".$compartment->freeDate.", Rows left: ".$compartment->rowsLeft.".";
            }
    }


    public function actionSetdate($date)
    {
        $crop = NumcropHasCompartment::find()->andFilterWhere(['compartment_idCompartment' => $date])->max('numcrop_cropnum');
        $data = NumcropHasCompartment::find()->andFilterWhere([
            'compartment_idCompartment' => explode(",", $date)[0],
            'numcrop_cropnum' => ($crop-1)
        ])->one();
        echo date('d-m-Y', strtotime("$data->freeDate - 6 day"));
    }

    /**
    * Allows to set able or disable an order for the evaluation.
     */
    public function actionChange($id){
        $order = Order::findOne(["idorder" => $id]);
        if($order->selector == "Active"){
            $order->selector = "Inactive";
            echo "selector: ".
                $order->selector;
            $order->save();
        }else{
            $order->selector = "Active";
            echo "selector: ".
                $order->selector;
            $order->save();
        }
    }

    /**
     * PDF export for Rijk Zwaan RPOMEX
     * good luck man.
     */
    public function actionPdf(){

        $excel = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [
                'Information' => [
                    'class' => 'codemix\excelexport\ActiveExcelSheet',
                    'query' => Order::find(),
                    'callbacks' => [
                        // $cell is a PHPExcel_Cell object
                        'A' => function ($cell) {
                            $cell->getStyle()->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                ],
                                'borders' => [
                                    'top' => [
                                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                    ],
                                    'bottom' => [
                                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                    ],
                                    'left' => [
                                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                    ],
                                    'right' => [
                                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                    ],
                                ],
                            ]);
                        },
                        ]
                ]
            ]
        ]);
        $excel->getWorkbook()->setActiveSheetIndex(0);
//        $excel->getActiveSheet()->getStyle('B6')->getFill()->getStartColor()->setARGB('FFFF0000');

        $excel->getWorkbook()
            ->getSheet()
            ->getStyle('B1')
            ->getFont()
            ->getColor()
            ->setARGB(\PHPExcel_Style_Color::COLOR_RED);
        $excel->getWorkbook()
            ->getSheet()
            ->getStyle('c3')
            ->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
        $excel->getWorkbook()
            ->getSheet()
            ->getStyle('B2:B5')
            ->getFont()
            ->getColor()
            ->setARGB(\PHPExcel_Style_Color::COLOR_DARKGREEN);
        $excel->getWorkbook()->getSheet()->getStyle('B2:B6')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $excel->getWorkbook()
            ->getSheet()
            ->getStyle('B2:B6')
            ->getFill()
            ->getStartcolor()
            ->setArgb('FFFF00');
        $excel->getWorkbook()
            ->getSheet()
            ->getStyle('B2:B6')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PHPExcel_Style_Border::BORDER_MEDIUM);
        $excel->getWorkbook()->getSheet()->getStyle('C2')
            ->getBorders()->getRight()->setBorderStyle('medium');
//        $excel->getPropierties()
//            ->setCreator("Matías Joaquín Tucci");
//            ->setLastModifyBy("Matías Joaquín Tucci")
//            ->setTitle("Rijk Zwaan PROMEX")
//            ->setSubject("Rijk Zwaan PROMEX 2017")
//            ->setDescription("Test")
//            ->setKeywords("extra text office excel 2007")
//            ->setCategory("Test result file");
        $excel->send('orders '.date("Y/m/d").'.xlsx');
        return $this->goHome();
    }

    /**
     * PDF export for orders
     * good luck man.
     */
    public function actionPdforder(){

        $excel = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [
                'Information' => [
                    'class' => 'codemix\excelexport\ActiveExcelSheet',
                    'query' => Order::find()->innerJoin('compartment'),
                ]
            ]
        ]);
        $excel->send('orders 2 '.date("Y/m/d").'.xlsx');

        return $this->render('/order%2Fhistorial', [
        ]);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * Lists all Order models.
     * @return mixed inicio planting
     */
    public function actionPlantingm()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['sowingDateM'=>SORT_ASC]];
        $dataProvider->query
            ->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['=','sowingDateM',date('Y-m-d')])
            ->andFilterWhere(['AND "A" =','`hybrid`.`variety` != `mother`.`variety`', "A"])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->all();
        return $this->render('seedsprocess\plantingm\index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionPlantingf()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['sowingDateF'=>SORT_ASC]];
        $dataProvider->query
            ->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['=', 'sowingDateF',date('Y-m-d')])
            ->andFilterWhere(['AND "A" =','`hybrid`.`variety` != `father`.`variety`', "A"])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->all();
        return $this->render('seedsprocess\plantingf\index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionArrive()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['ssRecDate'=>SORT_ASC]];
        $dataProvider->query
            ->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['=', 'ssRecDate',date('Y-m-d')])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->all();
        return $this->render('seedsprocess\arrive\index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionTransplantingm()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['transplantingM'=>SORT_ASC]];
        $dataProvider->query
            ->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['=', 'order.transplantingM',date('Y-m-d')])
            ->andFilterWhere(['AND "A" =','`hybrid`.`variety` != `mother`.`variety`', "A"])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->all();
        return $this->render('seedsprocess\transplantingm\index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionTransplantingf()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['transplantingF'=>SORT_ASC]];
        $dataProvider->query
            ->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['=', 'order.transplantingF',date('Y-m-d')])
            ->andFilterWhere(['AND "A" =','`hybrid`.`variety` != `father`.`variety`', "A"])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->all();
        return $this->render('seedsprocess\transplantingf\index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionOnlypc()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['transplantingF'=>SORT_ASC]];
        $dataProvider->query
            ->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['<=','order.pollinationF',date('Y-m-d')])
            ->andFilterWhere(['>=','order.pollinationU',date('Y-m-d')])
            ->andFilterWhere(['AND "A" =','`hybrid`.`variety` != `father`.`variety`', "A"])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->all();
        return $this->render('seedsprocess\onlypc\index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionCp()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['transplantingF'=>SORT_ASC]];
        $dataProvider->query
            ->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['<=','order.pollenColectF',date('Y-m-d')])
            ->andFilterWhere(['>=','order.pollenColectU',date('Y-m-d')])
            ->andFilterWhere(['AND "A" =','`hybrid`.`variety` != `mother`.`variety`', "A"])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->all();
        return $this->render('seedsprocess\cp\index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionHarvest()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['transplantingF'=>SORT_ASC]];
        $dataProvider->query
            ->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['<=','order.harvestF',date('Y-m-d')])
            ->andFilterWhere(['>=','order.harvestU',date('Y-m-d')])
            ->andFilterWhere(['AND "A" =','`hybrid`.`variety` != `father`.`variety`', "A"])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->all();
        return $this->render('seedsprocess\harvest\index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionClean()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['transplantingF'=>SORT_ASC]];
        $dataProvider->query
            //->andWhere(["=", "state", "Harvested plants"])
            ->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['>=','order.steamDesinfectionF',date('Y-m-d', strtotime( date('Y-m-d')." - 10 day"))])
            ->andFilterWhere(['<=','order.steamDesinfectionU',date('Y-m-d', strtotime( date('Y-m-d')." + 18 day"))])
            ->andFilterWhere(['>=','order.steamDesinfectionU',date('Y-m-d')])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->all();
        return $this->render('seedsprocess\clean\index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionFinish()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['selector'=>SORT_ASC]];
        $dataProvider->query
            ->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->andFilterWhere(['<=','steamDesinfectionU',date("Y-m-d")])->all();

        return $this->render('seedsprocess\finish\index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionImportExcelOrders(){

        // Consiguiendo el archivo:

        $inputFile = 'uploads/order1.xlsx   ';

        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
        }catch (Exception $e){
            die('Error uploading the excel file');
        }

        // Poniendo el primer excel

        $orders = $objPHPExcel->getSheet(0);
        $highestRow = $orders->getHighestRow();
        $highestColumn = $orders->getHighestColumn();

        echo "Order 1<br>";
        for ($row = 1; $row <= $highestRow; $row++){
            if ($row < 6){
                continue;
            }

            echo $row.") ";

            $rowData = $orders->rangeToArray('A'.$row.':'.'M'.$row, NULL, TRUE, FALSE);
            $rowData2 = $orders->rangeToArray('Q'.$row.':AS'.$row, NULL, TRUE, FALSE);

            $order = new Order();
            if ($rowData[0][2] == NULL){
                echo $rowData[0][2]." == NULL <br>";
                continue;
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }
            if (substr($hybrid, 2) > 999){
                $father = Father::findOne(['variety' =>$hybrid]);
                $mother = Mother::findOne(['variety' =>$hybrid]);

                $hybrid = new Hybrid();
                $hybrid->Crop_idcrops = 2;
                $hybrid->Father_idFather = $father->idFather;
                $hybrid->Mother_idMother = $mother->idMother;
                $hybrid->variety = $father->variety;
                $hybrid->save();
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }

            $hybrid = Hybrid::findOne(['variety' =>$hybrid]);
            $compartment = Compartment::findOne(['compNum' =>$rowData2[0][0]]);

            if ($rowData[0][7] == NULL ||
                $rowData[0][7] == "cancelled!" ||
                $rowData[0][7] == "cancelled" ||
                strpos($rowData[0][7], "-") ||
                $rowData[0][7] < 1000 ){
                $reqDelD = date("Y-m-d");
            }else{
                $reqDelD = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][7]));
            }
            if ($rowData[0][8] == NULL ||
                $rowData[0][8] == "cancelled!" ||
                $rowData[0][8] == "cancelled" ||
                strpos($rowData[0][8], "-") ||
                $rowData[0][8] < 1000 ){
                $orderDate = date("Y-m-d");
            }else{
                $orderDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8]));
            }
            if ($rowData2[0][13] == NULL ||
                $rowData2[0][13] == "cancelled!" ||
                $rowData2[0][13] == "cancelled" ||
                strpos($rowData2[0][13], "-") ||
                $rowData2[0][13] < 1000 ){
                $sowM = date("Y-m-d");
            }else{
                $sowM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][13]));
            }
            if ($rowData2[0][14] == NULL ||
                $rowData2[0][14] == "cancelled!" ||
                $rowData2[0][14] == "cancelled" ||
                strpos($rowData2[0][14], "-") ||
                $rowData2[0][14] < 1000 ){
                $sowF = date("Y-m-d");
            }else{
                $sowF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][14]));
            }
            if ($rowData2[0][17] == NULL ||
                $rowData2[0][17] == "cancelled!" ||
                $rowData2[0][17] == "cancelled" ||
                strpos($rowData2[0][17], "-") ||
                $rowData2[0][17] < 1000 ){
                $tM = date("Y-m-d");
            }else{
                $tM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][17]));
            }
            if ($rowData2[0][19] == NULL ||
                $rowData2[0][19] == "cancelled!" ||
                $rowData2[0][19] == "cancelled" ||
                strpos($rowData2[0][19], "-") ||
                $rowData2[0][19] < 1000 ){
                $tF = date("Y-m-d");
            }else{
                $tF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][19]));
            }
            if ($rowData2[0][21] == NULL ||
                $rowData2[0][21] == "cancelled!" ||
                $rowData2[0][21] == "cancelled" ||
                strpos($rowData2[0][21], "-") ||
                $rowData2[0][21] < 1000 ){
                $polinF = date("Y-m-d");
            }else{
                $polinF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][21]));
            }
            if ($rowData2[0][22] == NULL ||
                $rowData2[0][22] == "cancelled!" ||
                $rowData2[0][22] == "cancelled" ||
                strpos($rowData2[0][22], "-") ||
                $rowData2[0][22] < 1000 ){
                $plinU = date("Y-m-d");
            }else{
                $plinU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][22]));
            }
            if ($rowData2[0][23] == NULL ||
                $rowData2[0][23] == "cancelled!" ||
                $rowData2[0][23] == "cancelled" ||
                strpos($rowData2[0][23], "-") ||
                $rowData2[0][23] < 1000 ){
                $hF = date("Y-m-d");
            }else{
                $hF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][23]));
            }
            if ($rowData2[0][24] == NULL ||
                $rowData2[0][24] == "cancelled!" ||
                $rowData2[0][24] == "cancelled" ||
                strpos($rowData2[0][24], "-") ||
                $rowData2[0][24] < 1000 ){
                $hU = date("Y-m-d");
            }else{
                $hU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][24]));
            }
            if ($rowData2[0][26] == NULL ||
                $rowData2[0][26] == "cancelled!" ||
                $rowData2[0][26] == "cancelled" ||
                strpos($rowData2[0][26], "-") ||
                $rowData2[0][26] < 1000 ){
                $cleaning = date("Y-m-d");
            }else{
                $cleaning = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][26]));
            }

            if (empty($hybrid)){
                echo "Not found: ".$rowData[0][2]."<br>";
                continue;
            }else{
                //toda la asignación de valores;
                $order->Hybrid_idHybrid = $hybrid->idHybrid;
            }

            $order->numCrop = $rowData[0][0];
            $order->orderKg = $rowData[0][4];
            $order->gpOrder = $rowData[0][5];
            $order->ReqDeliveryDate = $reqDelD;
            $order->orderDate = $orderDate;
            $order->contractNumber = $rowData[0][9];
            if (!empty($rowData[0][10])){
                if (!($rowData[0][10] =='yes')){
                    $order->ssRecDate = $rowData[0][10];
                }
            }

            $order->compartment_idCompartment = $compartment->idCompartment;
            $numhcomps = NumcropHasCompartment::findOne(['compartment_idCompartment' => $order->compartment_idCompartment, 'numcrop_cropnum' => $order->numCrop]);
            if (empty($numhcomps)){
                $numhcomp = new NumcropHasCompartment();
                $numhcomp->compartment_idCompartment = $order->compartment_idCompartment;
                $numhcomp->crop_idcrops = $order->hybridIdHybr->Crop_idcrops;
                $numhcomp->numcrop_cropnum = $order->numCrop;
                $numhcomp->createDate = date('Y-m-d');
                $numhcomp->lastUpdatedDate = date('Y-m-d');
                $numhcomp->rowsLeft = 0;
                if ($order->compartmentIdCompartment->compNum == 111 ||
                    $order->compartmentIdCompartment->compNum == 116 ||
                    $order->compartmentIdCompartment->compNum == 121 ||
                    $order->compartmentIdCompartment->compNum == 126 ||
                    $order->compartmentIdCompartment->compNum == 131 ||
                    $order->compartmentIdCompartment->compNum == 136){
                    $numhcomp->rowsOccupied = 40;
                }else{
                    $numhcomp->rowsOccupied = 60;
                }
                $numhcomp->save();
                echo "numcrop Error: ";
                print_r($numhcomp->getErrors());
                echo "<br>";
                echo $order->numCrop;
            }else{
                if (date('Y-m-d', strtotime($numhcomps->freeDate))
                    < date('Y-m-d', strtotime( $cleaning." + 1 day"))){
                    $numhcomps->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                    $numhcomps->save();
                }
            }

            $order->numRows = $rowData2[0][1];
            $order->plantingDistance = 50;
            $order->netNumOfPlantsF = floor($rowData2[0][8]);
            $order->netNumOfPlantsM = floor($rowData2[0][9]);
            $order->nurseryF = floor($rowData2[0][10]);
            $order->nurseryM = floor($rowData2[0][11]);
            if ($rowData2[0][12] == "Check!"){
                $order->check = $rowData2[0][12];
            }else{
                $order->check = "Great, no problem.";
            }

            $order->sowingDateM = date('Y-m-d', strtotime($sowM));
            $order->sowingDateF = date('Y-m-d', strtotime($sowF));
            $order->transplantingM= date('Y-m-d', strtotime($tF));
            $order->realisedNrOfPlantsM = $rowData2[0][18];
            $order->transplantingF = date('Y-m-d', strtotime($tM));
            $order->realisedNrOfPlantsF = $rowData2[0][20];
            $order->pollinationF = date('Y-m-d', strtotime($polinF));
            $order->pollinationU = date('Y-m-d', strtotime($plinU));
            $order->harvestF = date('Y-m-d', strtotime($hF));
            $order->harvestU = date('Y-m-d', strtotime($hU));
            $order->pollenColectF = $order->harvestF;
            $order->pollenColectU = $order->harvestU;
            $order->steamDesinfectionF = $order->harvestU;
            $order->steamDesinfectionU = date('Y-m-d', strtotime($cleaning));
            $order->save();
            echo "Error: ";
            print_r($order->getErrors());
            echo "<br>";
        }

        echo "<br>";



        // SEGUNDO ARCHIVO
        echo "Segundo archivo <br>";
        // Consiguiendo el archivo:

        $inputFile = 'uploads/order2.xlsx';

        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
        }catch (Exception $e){
            die('Error uploading the excel file');
        }

        // Poniendo el segundo excel

        $orders = $objPHPExcel->getSheet(0);
        $highestRow = $orders->getHighestRow();
        $highestColumn = $orders->getHighestColumn();

        echo "Order 2<br>";
        for ($row = 1; $row <= $highestRow; $row++){
            if ($row < 5){
                continue;
            }
            echo $row.") ";

            $rowData = $orders->rangeToArray('A'.$row.':'.'M'.$row, NULL, TRUE, FALSE);
            $rowData2 = $orders->rangeToArray('Q'.$row.':AP'.$row, NULL, TRUE, FALSE);

            $order = new Order();
            if ($rowData[0][2] == NULL){
                echo $rowData[0][2]." == NULL <br>";
                continue;
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }
            if (substr($hybrid, 2) > 999){
                $father = Father::findOne(['variety' =>$hybrid]);
                $mother = Mother::findOne(['variety' =>$hybrid]);

                $hybrid = new Hybrid();
                $hybrid->Crop_idcrops = 2;
                $hybrid->Father_idFather = $father->idFather;
                $hybrid->Mother_idMother = $mother->idMother;
                $hybrid->variety = $father->variety;
                $hybrid->save();
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }

            $hybrid = Hybrid::findOne(['variety' =>$hybrid]);
            $compartment = Compartment::findOne(['compNum' =>$rowData2[0][0]]);

            if ($rowData[0][7] == NULL ||
                $rowData[0][7] == "cancelled!" ||
                $rowData[0][7] == "cancelled" ||
                strpos($rowData[0][7], "-") ||
                $rowData[0][7] < 1000 ){
                $reqDelD = date("Y-m-d");
            }else{
                $reqDelD = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][7]));
            }
            if ($rowData[0][8] == NULL ||
                $rowData[0][8] == "cancelled!" ||
                $rowData[0][8] == "cancelled" ||
                strpos($rowData[0][8], "-") ||
                $rowData[0][8] < 1000 ){
                $orderDate = date("Y-m-d");
            }else{
                $orderDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8]));
            }
            if ($rowData2[0][13] == NULL ||
                $rowData2[0][13] == "cancelled!" ||
                $rowData2[0][13] == "cancelled" ||
                strpos($rowData2[0][13], "-") ||
                $rowData2[0][13] < 1000 ){
                $sowM = date("Y-m-d");
            }else{
                $sowM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][13]));
            }
            if ($rowData2[0][14] == NULL ||
                $rowData2[0][14] == "cancelled!" ||
                $rowData2[0][14] == "cancelled" ||
                strpos($rowData2[0][14], "-") ||
                $rowData2[0][14] < 1000 ){
                $sowF = date("Y-m-d");
            }else{
                $sowF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][14]));
            }
            if ($rowData2[0][15] == NULL ||
                $rowData2[0][15] == "cancelled!" ||
                $rowData2[0][15] == "cancelled" ||
                strpos($rowData2[0][15], "-") ||
                $rowData2[0][15] < 1000 ){
                $tM = date("Y-m-d");
            }else{
                $tM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][15]));
            }
            if ($rowData2[0][17] == NULL ||
                $rowData2[0][17] == "cancelled!" ||
                $rowData2[0][17] == "cancelled" ||
                strpos($rowData2[0][17], "-") ||
                $rowData2[0][17] < 1000 ){
                $tF = date("Y-m-d");
            }else{
                $tF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][17]));
            }
            if ($rowData2[0][19] == NULL ||
                $rowData2[0][19] == "cancelled!" ||
                $rowData2[0][19] == "cancelled" ||
                strpos($rowData2[0][19], "-") ||
                $rowData2[0][19] < 1000 ){
                $polinF = date("Y-m-d");
            }else{
                $polinF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][19]));
            }
            if ($rowData2[0][20] == NULL ||
                $rowData2[0][20] == "cancelled!" ||
                $rowData2[0][20] == "cancelled" ||
                strpos($rowData2[0][20], "-") ||
                $rowData2[0][20] < 1000 ){
                $plinU = date("Y-m-d");
            }else{
                $plinU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][20]));
            }
            if ($rowData2[0][21] == NULL ||
                $rowData2[0][21] == "cancelled!" ||
                $rowData2[0][21] == "cancelled" ||
                strpos($rowData2[0][21], "-") ||
                $rowData2[0][21] < 1000 ){
                $hF = date("Y-m-d");
            }else{
                $hF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][21]));
            }
            if ($rowData2[0][22] == NULL ||
                $rowData2[0][22] == "cancelled!" ||
                $rowData2[0][22] == "cancelled" ||
                strpos($rowData2[0][22], "-") ||
                $rowData2[0][22] < 1000 ){
                $hU = date("Y-m-d");
            }else{
                $hU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][22]));
            }
            if ($rowData2[0][23] == NULL ||
                $rowData2[0][23] == "cancelled!" ||
                $rowData2[0][23] == "cancelled" ||
                strpos($rowData2[0][23], "-") ||
                $rowData2[0][23] < 1000 ){
                $cleaning = date("Y-m-d");
            }else{
                $cleaning = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][23]));
            }

            if (empty($hybrid)){
                echo "Not found: ".$rowData[0][2]."<br>";
                continue;
            }else{
                //toda la asignación de valores;
                $order->Hybrid_idHybrid = $hybrid->idHybrid;
            }
            if($rowData[0][2] == NULL){
                continue;
            }
            $order->numCrop = $rowData[0][0];
            $order->orderKg = $rowData[0][4];
            $order->gpOrder = $rowData[0][5];
            $order->ReqDeliveryDate = $reqDelD;
            $order->orderDate = $orderDate;
            $order->contractNumber = $rowData[0][9];
            if (!empty($rowData[0][10])){
                if (!($rowData[0][10] =='yes')){
                    $order->ssRecDate = $rowData[0][10];
                }
            }
            $order->compartment_idCompartment = $compartment->idCompartment;
            $numhcomps = NumcropHasCompartment::findOne(['compartment_idCompartment' => $order->compartment_idCompartment, 'numcrop_cropnum' => $order->numCrop]);

            if (empty($numhcomps)){
                $numhcomp = new NumcropHasCompartment();
                $numhcomp->compartment_idCompartment = $order->compartment_idCompartment;
                $numhcomp->crop_idcrops = $order->hybridIdHybr->Crop_idcrops;
                $numhcomp->numcrop_cropnum = $order->numCrop;
                $numhcomp->createDate = date('Y-m-d');
                $numhcomp->lastUpdatedDate = date('Y-m-d');
                $numhcomp->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                $numhcomp->rowsLeft = 0;
                if ($order->compartmentIdCompartment->compNum == 111 ||
                    $order->compartmentIdCompartment->compNum == 116 ||
                    $order->compartmentIdCompartment->compNum == 121 ||
                    $order->compartmentIdCompartment->compNum == 126 ||
                    $order->compartmentIdCompartment->compNum == 131 ||
                    $order->compartmentIdCompartment->compNum == 136){
                    $numhcomp->rowsOccupied = 40;
                }else{
                    $numhcomp->rowsOccupied = 60;
                }
                $numhcomp->save();
            }else{
                if (date('Y-m-d', strtotime($numhcomps->freeDate))
                    < date('Y-m-d', strtotime( $cleaning." + 1 day"))){
                    $numhcomps->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                    $numhcomps->save();
                }
            }
            $order->numRows = $rowData2[0][1];
            $order->plantingDistance = 50;
            $order->netNumOfPlantsF = floor($rowData2[0][8]);
            $order->netNumOfPlantsM = floor($rowData2[0][9]);
            $order->nurseryF = floor($rowData2[0][10]);
            $order->nurseryM = floor($rowData2[0][11]);
            if ($rowData2[0][12] == "Check!"){
                $order->check = $rowData2[0][12];
            }else{
                $order->check = "Great, no problem.";
            }

            $order->sowingDateM = date('Y-m-d', strtotime($sowF));
            $order->sowingDateF = date('Y-m-d', strtotime($sowM));
            $order->transplantingM= date('Y-m-d', strtotime($tM));
            $order->realisedNrOfPlantsM = $rowData2[0][16];
            $order->transplantingF = date('Y-m-d', strtotime($tF));
            $order->realisedNrOfPlantsF = $rowData2[0][18];
            $order->pollinationF = date('Y-m-d', strtotime($polinF));
            $order->pollinationU = date('Y-m-d', strtotime($plinU));
            $order->harvestF = date('Y-m-d', strtotime($hF));
            $order->harvestU = date('Y-m-d', strtotime($hU));
            $order->pollenColectF = $order->harvestF;
            $order->pollenColectU = $order->harvestU;
            $order->steamDesinfectionF = $order->harvestU;
            $order->steamDesinfectionU = date('Y-m-d', strtotime($cleaning));
            $order->save();
            echo "Error: ";
            print_r($order->getErrors());
            echo "<br>";
        }
        echo "<br>";



        // TERCER ARCHIVO
        echo "TERCER archivo <br>";
        // Consiguiendo el archivo:

        $inputFile = 'uploads/order3.xlsx   ';

        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
        }catch (Exception $e){
            die('Error uploading the excel file');
        }

        // Poniendo el tercer excel

        $orders = $objPHPExcel->getSheet(0);
        $highestRow = $orders->getHighestRow();
        $highestColumn = $orders->getHighestColumn();

        echo "Order 3<br>";
        for ($row = 1; $row <= $highestRow; $row++){
            if ($row < 5){
                continue;
            }
            echo $row.") ";

            $rowData = $orders->rangeToArray('A'.$row.':'.'M'.$row, NULL, TRUE, FALSE);
            $rowData2 = $orders->rangeToArray('Q'.$row.':AP'.$row, NULL, TRUE, FALSE);



            $order = new Order();
            if ($rowData[0][2] == NULL){
                echo $rowData[0][2]." == NULL <br>";
                continue;
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }
            if (substr($hybrid, 2) > 999){
                $father = Father::findOne(['variety' =>$hybrid]);
                $mother = Mother::findOne(['variety' =>$hybrid]);

                $hybrid = new Hybrid();
                $hybrid->Crop_idcrops = 2;
                $hybrid->Father_idFather = $father->idFather;
                $hybrid->Mother_idMother = $mother->idMother;
                $hybrid->variety = $father->variety;
                $hybrid->save();
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }

            $hybrid = Hybrid::findOne(['variety' =>$hybrid]);
            $compartment = Compartment::findOne(['compNum' =>$rowData2[0][0]]);

            if ($rowData[0][7] == NULL ||
                $rowData[0][7] == "cancelled!" ||
                $rowData[0][7] == "cancelled" ||
                strpos($rowData[0][7], "-") ||
                $rowData[0][7] < 1000 ){
                $reqDelD = date("Y-m-d");
            }else{
                $reqDelD = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][7]));
            }
            if ($rowData[0][8] == NULL ||
                $rowData[0][8] == "cancelled!" ||
                $rowData[0][8] == "cancelled" ||
                strpos($rowData[0][8], "-") ||
                $rowData[0][8] < 1000 ){
                $orderDate = date("Y-m-d");
            }else{
                $orderDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8]));
            }
            if ($rowData2[0][13] == NULL ||
                $rowData2[0][13] == "cancelled!" ||
                $rowData2[0][13] == "cancelled" ||
                strpos($rowData2[0][13], "-") ||
                $rowData2[0][13] < 1000 ){
                $sowM = date("Y-m-d");
            }else{
                $sowM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][13]));
            }
            if ($rowData2[0][14] == NULL ||
                $rowData2[0][14] == "cancelled!" ||
                $rowData2[0][14] == "cancelled" ||
                strpos($rowData2[0][14], "-") ||
                $rowData2[0][14] < 1000 ){
                $sowF = date("Y-m-d");
            }else{
                $sowF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][14]));
            }
            if ($rowData2[0][15] == NULL ||
                $rowData2[0][15] == "cancelled!" ||
                $rowData2[0][15] == "cancelled" ||
                strpos($rowData2[0][15], "-") ||
                $rowData2[0][15] < 1000 ){
                $tM = date("Y-m-d");
            }else{
                $tM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][15]));
            }
            if ($rowData2[0][17] == NULL ||
                $rowData2[0][17] == "cancelled!" ||
                $rowData2[0][17] == "cancelled" ||
                strpos($rowData2[0][17], "-") ||
                $rowData2[0][17] < 1000 ){
                $tF = date("Y-m-d");
            }else{
                $tF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][17]));
            }
            if ($rowData2[0][21] == NULL ||
                $rowData2[0][21] == "cancelled!" ||
                $rowData2[0][21] == "cancelled" ||
                strpos($rowData2[0][21], "-") ||
                $rowData2[0][21] < 1000 ){
                $polinF = date("Y-m-d");
            }else{
                $polinF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][21]));
            }
            if ($rowData2[0][22] == NULL ||
                $rowData2[0][22] == "cancelled!" ||
                $rowData2[0][22] == "cancelled" ||
                strpos($rowData2[0][22], "-") ||
                $rowData2[0][22] < 1000 ){
                $plinU = date("Y-m-d");
            }else{
                $plinU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][22]));
            }
            if ($rowData2[0][23] == NULL ||
                $rowData2[0][23] == "cancelled!" ||
                $rowData2[0][23] == "cancelled" ||
                strpos($rowData2[0][23], "-") ||
                $rowData2[0][23] < 1000 ){
                $hF = date("Y-m-d");
            }else{
                $hF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][23]));
            }
            if ($rowData2[0][24] == NULL ||
                $rowData2[0][24] == "cancelled!" ||
                $rowData2[0][24] == "cancelled" ||
                strpos($rowData2[0][24], "-") ||
                $rowData2[0][24] < 1000 ){
                $hU = date("Y-m-d");
            }else{
                $hU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][24]));
            }
            if ($rowData2[0][25] == NULL ||
                $rowData2[0][25] == "cancelled!" ||
                $rowData2[0][25] == "cancelled" ||
                strpos($rowData2[0][25], "-") ||
                $rowData2[0][25] < 1000 ){
                $cleaning = date("Y-m-d");
            }else{
                $cleaning = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][25]));
            }

            if (empty($hybrid)){
                echo "Not found: ".$rowData[0][2]."<br>";
                continue;
            }else{
                //toda la asignación de valores;
                $order->Hybrid_idHybrid = $hybrid->idHybrid;
            }
            if($rowData[0][2] == NULL){
                continue;
            }
            $order->numCrop = $rowData[0][0];
            $order->orderKg = $rowData[0][4];
            $order->gpOrder = $rowData[0][5];
            $order->ReqDeliveryDate = $reqDelD;
            $order->orderDate = $orderDate;
            $order->contractNumber = $rowData[0][9];
            if (!empty($rowData[0][10])){
                if (!($rowData[0][10] =='yes')){
                    $order->ssRecDate = $rowData[0][10];
                }
            }
            $order->compartment_idCompartment = $compartment->idCompartment;
            $numhcomps = NumcropHasCompartment::findOne(['compartment_idCompartment' => $order->compartment_idCompartment, 'numcrop_cropnum' => $order->numCrop]);

            if (empty($numhcomps)){
                $numhcomp = new NumcropHasCompartment();
                $numhcomp->compartment_idCompartment = $order->compartment_idCompartment;
                $numhcomp->crop_idcrops = $order->hybridIdHybr->Crop_idcrops;
                $numhcomp->numcrop_cropnum = $order->numCrop;
                $numhcomp->createDate = date('Y-m-d');
                $numhcomp->lastUpdatedDate = date('Y-m-d');
                $numhcomp->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                $numhcomp->rowsLeft = 0;
                if ($order->compartmentIdCompartment->compNum == 111 ||
                    $order->compartmentIdCompartment->compNum == 116 ||
                    $order->compartmentIdCompartment->compNum == 121 ||
                    $order->compartmentIdCompartment->compNum == 126 ||
                    $order->compartmentIdCompartment->compNum == 131 ||
                    $order->compartmentIdCompartment->compNum == 136){
                    $numhcomp->rowsOccupied = 40;
                }else{
                    $numhcomp->rowsOccupied = 60;
                }
                $numhcomp->save();
            }else{
                if (date('Y-m-d', strtotime($numhcomps->freeDate))
                    < date('Y-m-d', strtotime( $cleaning." + 1 day"))){
                    $numhcomps->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                    $numhcomps->save();
                }
            }
            $order->numRows = $rowData2[0][1];
            $order->plantingDistance = 50;
            $order->netNumOfPlantsF = floor($rowData2[0][8]);
            $order->netNumOfPlantsM = floor($rowData2[0][9]);
            $order->nurseryF = floor($rowData2[0][10]);
            $order->nurseryM = floor($rowData2[0][11]);
            if ($rowData2[0][12] == "Check!"){
                $order->check = $rowData2[0][12];
            }else{
                $order->check = "Great, no problem.";
            }

            $order->sowingDateM = date('Y-m-d', strtotime($sowF));
            $order->sowingDateF = date('Y-m-d', strtotime($sowM));
            $order->transplantingM= date('Y-m-d', strtotime($tM));
            $order->realisedNrOfPlantsM = $rowData2[0][16];
            $order->transplantingF = date('Y-m-d', strtotime($tF));
            $order->realisedNrOfPlantsF = $rowData2[0][18];
            $order->pollinationF = date('Y-m-d', strtotime($polinF));
            $order->pollinationU = date('Y-m-d', strtotime($plinU));
            $order->harvestF = date('Y-m-d', strtotime($hF));
            $order->harvestU = date('Y-m-d', strtotime($hU));
            $order->pollenColectF = $order->harvestF;
            $order->pollenColectU = $order->harvestU;
            $order->steamDesinfectionF = $order->harvestU;
            $order->steamDesinfectionU = date('Y-m-d', strtotime($cleaning));
            $order->save();
            echo "Error: ";
            print_r($order->getErrors());
            echo "<br>";
        }



        // Cuarto ARCHIVO
        echo "<br>CUARTO archivo <br>";
        // Consiguiendo el archivo:

        $inputFile = 'uploads/order4.xlsx   ';

        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
        }catch (Exception $e){
            die('Error uploading the excel file');
        }

        // Poniendo el cuarto excel

        $orders = $objPHPExcel->getSheet(0);
        $highestRow = $orders->getHighestRow();
        $highestColumn = $orders->getHighestColumn();

        echo "Order 4<br>";
        for ($row = 1; $row <= $highestRow; $row++) {
            if ($row < 5) {
                continue;
            }

            $rowData = $orders->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData2 = $orders->rangeToArray('S' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            echo $row.") ";
            if ($row == 498){
                echo $rowData[0][8];
//                die;
            }



            $order = new Order();
            if ($rowData[0][2] == NULL){
                echo $rowData[0][2]." == NULL <br>";
                continue;
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }
            if (substr($hybrid, 2) > 999){
                $father = Father::findOne(['variety' =>$hybrid]);
                $mother = Mother::findOne(['variety' =>$hybrid]);

                $hybrid = new Hybrid();
                $hybrid->Crop_idcrops = 2;
                $hybrid->Father_idFather = $father->idFather;
                $hybrid->Mother_idMother = $mother->idMother;
                $hybrid->variety = $father->variety;
                $hybrid->save();
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }

            $hybrid = Hybrid::findOne(['variety' =>$hybrid]);
            $compartment = Compartment::findOne(['compNum' =>$rowData2[0][0]]);

            if ($rowData[0][7] == NULL ||
                $rowData[0][7] == "cancelled!" ||
                $rowData[0][7] == "cancelled" ||
                strpos($rowData[0][7], "-") ||
                $rowData[0][7] < 1000 ){
                $reqDelD = date("Y-m-d");
            }else{
                $reqDelD = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][7]));
            }
            if ($rowData[0][8] == NULL ||
                $rowData[0][8] == "cancelled!" ||
                $rowData[0][8] == "cancelled" ||
                strpos($rowData[0][8], "-") ||
                $rowData[0][8] < 1000 ){
                $orderDate = date("Y-m-d");
            }else{
                $orderDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8]));
            }
            if ($rowData2[0][13] == NULL ||
                $rowData2[0][13] == "cancelled!" ||
                $rowData2[0][13] == "cancelled" ||
                strpos($rowData2[0][13], "-") ||
                $rowData2[0][13] < 1000 ){
                $sowM = date("Y-m-d");
            }else{
                $sowM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][13]));
            }
            if ($rowData2[0][14] == NULL ||
                $rowData2[0][14] == "cancelled!" ||
                $rowData2[0][14] == "cancelled" ||
                strpos($rowData2[0][14], "-") ||
                $rowData2[0][14] < 1000 ){
                $sowF = date("Y-m-d");
            }else{
                $sowF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][14]));
            }
            if ($rowData2[0][15] == NULL ||
                $rowData2[0][15] == "cancelled!" ||
                $rowData2[0][15] == "cancelled" ||
                strpos($rowData2[0][15], "-") ||
                $rowData2[0][15] < 1000 ){
                $tM = date("Y-m-d");
            }else{
                $tM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][15]));
            }
            if ($rowData2[0][17] == NULL ||
                $rowData2[0][17] == "cancelled!" ||
                $rowData2[0][17] == "cancelled" ||
                strpos($rowData2[0][17], "-") ||
                $rowData2[0][17] < 1000 ){
                $tF = date("Y-m-d");
            }else{
                $tF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][17]));
            }
            if ($rowData2[0][21] == NULL ||
                $rowData2[0][21] == "cancelled!" ||
                $rowData2[0][21] == "cancelled" ||
                strpos($rowData2[0][21], "-") ||
                $rowData2[0][21] < 1000 ){
                $polinF = date("Y-m-d");
            }else{
                $polinF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][21]));
            }
            if ($rowData2[0][22] == NULL ||
                $rowData2[0][22] == "cancelled!" ||
                $rowData2[0][22] == "cancelled" ||
                strpos($rowData2[0][22], "-") ||
                $rowData2[0][22] < 1000 ){
                $plinU = date("Y-m-d");
            }else{
                $plinU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][22]));
            }
            if ($rowData2[0][23] == NULL ||
                $rowData2[0][23] == "cancelled!" ||
                $rowData2[0][23] == "cancelled" ||
                strpos($rowData2[0][23], "-") ||
                $rowData2[0][23] < 1000 ){
                $hF = date("Y-m-d");
            }else{
                $hF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][23]));
            }
            if ($rowData2[0][24] == NULL ||
                $rowData2[0][24] == "cancelled!" ||
                $rowData2[0][24] == "cancelled" ||
                strpos($rowData2[0][24], "-") ||
                $rowData2[0][24] < 1000 ){
                $hU = date("Y-m-d");
            }else{
                $hU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][24]));
            }
            if ($rowData2[0][25] == NULL ||
                $rowData2[0][25] == "cancelled!" ||
                $rowData2[0][25] == "cancelled" ||
                strpos($rowData2[0][25], "-") ||
                $rowData2[0][25] < 1000 ){
                $cleaning = date("Y-m-d");
            }else{
                $cleaning = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][25]));
            }

            if ($rowData2[0][0] == NULL){
                continue;
            }

            if ($rowData2[0][21] != NULL){
                $order->extractedPlantsF = $rowData2[0][21];
            }
            if ($rowData2[0][22] != NULL){
                $order->realisedNrOfPlantsF = $rowData2[0][22];
            }


            if (empty($hybrid)){
                echo "Not found: ".$rowData[0][2]."<br>";
                continue;
            }else{
                //toda la asignación de valores;
                $order->Hybrid_idHybrid = $hybrid->idHybrid;
            }
            if($rowData[0][2] == NULL){
                continue;
            }
            $order->numCrop = $rowData[0][0];
            $order->orderKg = $rowData[0][4];
            $order->gpOrder = $rowData[0][5];
            $order->ReqDeliveryDate = $reqDelD;
            $order->orderDate = $orderDate;
            $order->contractNumber = $rowData[0][9];
            if (!empty($rowData[0][10])){
                if (!($rowData[0][10] =='yes')){
                    $order->ssRecDate = $rowData[0][10];
                }
            }
            $order->compartment_idCompartment = $compartment->idCompartment;
            $numhcomps = NumcropHasCompartment::findOne(['compartment_idCompartment' => $order->compartment_idCompartment, 'numcrop_cropnum' => $order->numCrop]);

            if (empty($numhcomps)){
                $numhcomp = new NumcropHasCompartment();
                $numhcomp->compartment_idCompartment = $order->compartment_idCompartment;
                $numhcomp->crop_idcrops = $order->hybridIdHybr->Crop_idcrops;
                $numhcomp->numcrop_cropnum = $order->numCrop;
                $numhcomp->createDate = date('Y-m-d');
                $numhcomp->lastUpdatedDate = date('Y-m-d');
                $numhcomp->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                $numhcomp->rowsLeft = 0;
                if ($order->compartmentIdCompartment->compNum == 111 ||
                    $order->compartmentIdCompartment->compNum == 116 ||
                    $order->compartmentIdCompartment->compNum == 121 ||
                    $order->compartmentIdCompartment->compNum == 126 ||
                    $order->compartmentIdCompartment->compNum == 131 ||
                    $order->compartmentIdCompartment->compNum == 136){
                    $numhcomp->rowsOccupied = 40;
                }else{
                    $numhcomp->rowsOccupied = 60;
                }
                $numhcomp->save();
            }else{
                if (date('Y-m-d', strtotime($numhcomps->freeDate))
                    < date('Y-m-d', strtotime( $cleaning." + 1 day"))){
                    $numhcomps->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                    $numhcomps->save();
                }
            }
            $order->numRows = $rowData[0][19];
            $order->plantingDistance = 50;
            $order->netNumOfPlantsF = floor($rowData2[0][8]);
            $order->netNumOfPlantsM = floor($rowData2[0][9]);
            $order->sowingF = floor($rowData2[0][10]);
            $order->sowingM = floor($rowData2[0][11]);
            $order->nurseryF = floor($rowData2[0][12]);
            $order->nurseryM = floor($rowData2[0][13]);
            if ($rowData2[0][14] == "Check!"){
                $order->check = $rowData2[0][14];
            }else{
                $order->check = "Great, no problem.";
            }

            $order->sowingDateM = date('Y-m-d', strtotime($sowF));
            $order->sowingDateF = date('Y-m-d', strtotime($sowM));
            $order->transplantingM= date('Y-m-d', strtotime($tM));
            $order->realisedNrOfPlantsM = $rowData2[0][18];
            $order->transplantingF = date('Y-m-d', strtotime($tF));
            $order->realisedNrOfPlantsF = $rowData2[0][20];
            $order->pollinationF = date('Y-m-d', strtotime($polinF));
            $order->pollinationU = date('Y-m-d', strtotime($plinU));
            $order->harvestF = date('Y-m-d', strtotime($hF));
            $order->harvestU = date('Y-m-d', strtotime($hU));
            $order->pollenColectF = $order->harvestF;
            $order->pollenColectU = $order->harvestU;
            $order->steamDesinfectionF = $order->harvestU;
            $order->steamDesinfectionU = date('Y-m-d', strtotime($cleaning));
            $order->save();
            echo "Error: ";
            print_r($order->getErrors());
            echo "<br>";
        }



        // QUINTO  ARCHIVO
        echo "<br>QUINTO archivo <br>";
        // Consiguiendo el archivo:

        $inputFile = 'uploads/order5.xlsx   ';

        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
        }catch (Exception $e){
            die('Error uploading the excel file');
        }

        // Poniendo el QUINTO excel

        $orders = $objPHPExcel->getSheet(0);
        $highestRow = $orders->getHighestRow();
        $highestColumn = $orders->getHighestColumn();

        echo "<br>";
        for ($row = 1; $row <= $highestRow; $row++) {
            if ($row < 5) {
                continue;
            }

            $rowData = $orders->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData2 = $orders->rangeToArray('S' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            echo $row . ") ";

            if ($rowData[0][0] > 12) {
                continue;
            } else if (($rowData[0][0] == 12) &&
                (
                    $rowData[0][18] == 132 ||
                    $rowData[0][18] == 134 ||
                    $rowData[0][18] == 136
                )) {
                continue;
            }


            if ($row == 498){
                echo $rowData[0][8];
//                die;
            }



            $order = new Order();
            if ($rowData[0][2] == NULL){
                echo $rowData[0][2]." == NULL <br>";
                continue;
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }
            if (substr($hybrid, 2) > 999){
                $father = Father::findOne(['variety' =>$hybrid]);
                $mother = Mother::findOne(['variety' =>$hybrid]);

                $hybrid = new Hybrid();
                $hybrid->Crop_idcrops = 2;
                $hybrid->Father_idFather = $father->idFather;
                $hybrid->Mother_idMother = $mother->idMother;
                $hybrid->variety = $father->variety;
                $hybrid->save();
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }

            $hybrid = Hybrid::findOne(['variety' =>$hybrid]);
            $compartment = Compartment::findOne(['compNum' =>$rowData2[0][0]]);

            if ($rowData[0][7] == NULL ||
                $rowData[0][7] == "cancelled!" ||
                $rowData[0][7] == "cancelled" ||
                strpos($rowData[0][7], "-") ||
                $rowData[0][7] < 1000 ){
                $reqDelD = date("Y-m-d");
            }else{
                $reqDelD = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][7]));
            }
            if ($rowData[0][8] == NULL ||
                $rowData[0][8] == "cancelled!" ||
                $rowData[0][8] == "cancelled" ||
                strpos($rowData[0][8], "-") ||
                $rowData[0][8] < 1000 ){
                $orderDate = date("Y-m-d");
            }else{
                $orderDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8]));
            }
            if ($rowData2[0][13] == NULL ||
                $rowData2[0][13] == "cancelled!" ||
                $rowData2[0][13] == "cancelled" ||
                strpos($rowData2[0][13], "-") ||
                $rowData2[0][13] < 1000 ){
                $sowM = date("Y-m-d");
            }else{
                $sowM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][13]));
            }
            if ($rowData2[0][14] == NULL ||
                $rowData2[0][14] == "cancelled!" ||
                $rowData2[0][14] == "cancelled" ||
                strpos($rowData2[0][14], "-") ||
                $rowData2[0][14] < 1000 ){
                $sowF = date("Y-m-d");
            }else{
                $sowF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][14]));
            }
            if ($rowData2[0][15] == NULL ||
                $rowData2[0][15] == "cancelled!" ||
                $rowData2[0][15] == "cancelled" ||
                strpos($rowData2[0][15], "-") ||
                $rowData2[0][15] < 1000 ){
                $tM = date("Y-m-d");
            }else{
                $tM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][15]));
            }
            if ($rowData2[0][17] == NULL ||
                $rowData2[0][17] == "cancelled!" ||
                $rowData2[0][17] == "cancelled" ||
                strpos($rowData2[0][17], "-") ||
                $rowData2[0][17] < 1000 ){
                $tF = date("Y-m-d");
            }else{
                $tF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][17]));
            }
            if ($rowData2[0][21] == NULL ||
                $rowData2[0][21] == "cancelled!" ||
                $rowData2[0][21] == "cancelled" ||
                strpos($rowData2[0][21], "-") ||
                $rowData2[0][21] < 1000 ){
                $polinF = date("Y-m-d");
            }else{
                $polinF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][21]));
            }
            if ($rowData2[0][22] == NULL ||
                $rowData2[0][22] == "cancelled!" ||
                $rowData2[0][22] == "cancelled" ||
                strpos($rowData2[0][22], "-") ||
                $rowData2[0][22] < 1000 ){
                $plinU = date("Y-m-d");
            }else{
                $plinU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][22]));
            }
            if ($rowData2[0][23] == NULL ||
                $rowData2[0][23] == "cancelled!" ||
                $rowData2[0][23] == "cancelled" ||
                strpos($rowData2[0][23], "-") ||
                $rowData2[0][23] < 1000 ){
                $hF = date("Y-m-d");
            }else{
                $hF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][23]));
            }
            if ($rowData2[0][24] == NULL ||
                $rowData2[0][24] == "cancelled!" ||
                $rowData2[0][24] == "cancelled" ||
                strpos($rowData2[0][24], "-") ||
                $rowData2[0][24] < 1000 ){
                $hU = date("Y-m-d");
            }else{
                $hU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][24]));
            }
            if ($rowData2[0][25] == NULL ||
                $rowData2[0][25] == "cancelled!" ||
                $rowData2[0][25] == "cancelled" ||
                strpos($rowData2[0][25], "-") ||
                $rowData2[0][25] < 1000 ){
                $cleaning = date("Y-m-d");
            }else{
                $cleaning = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][25]));
            }

            if ($rowData2[0][0] == NULL || $rowData[0][0] == NULL){
                continue;
            }

            if ($rowData2[0][21] != NULL){
                $order->extractedPlantsF = floor($rowData2[0][21]);
            }

            if (empty($hybrid)){
                echo "Not found: ".$rowData[0][2]."<br>";
                continue;
            }else{
                //toda la asignación de valores;
                $order->Hybrid_idHybrid = $hybrid->idHybrid;
            }
            if($rowData[0][2] == NULL){
                continue;
            }
            $order->numCrop = $rowData[0][0];
            $order->orderKg = $rowData[0][4];
            $order->gpOrder = $rowData[0][5];
            $order->ReqDeliveryDate = $reqDelD;
            $order->orderDate = $orderDate;
            $order->contractNumber = $rowData[0][9];
            if (!empty($rowData[0][10])){
                if (!($rowData[0][10] =='yes')){
                    $order->ssRecDate = $rowData[0][10];
                }
            }
            $order->compartment_idCompartment = $compartment->idCompartment;
            $numhcomps = NumcropHasCompartment::findOne(['compartment_idCompartment' => $order->compartment_idCompartment, 'numcrop_cropnum' => $order->numCrop]);


            if (empty($numhcomps)){
                $numhcomp = new NumcropHasCompartment();
                $numhcomp->compartment_idCompartment = $order->compartment_idCompartment;
                $numhcomp->crop_idcrops = $order->hybridIdHybr->Crop_idcrops;
                $numhcomp->numcrop_cropnum = $order->numCrop;
                $numhcomp->createDate = date('Y-m-d');
                $numhcomp->lastUpdatedDate = date('Y-m-d');
                $numhcomp->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                $numhcomp->rowsLeft = 0;
                if ($order->compartmentIdCompartment->compNum == 111 ||
                    $order->compartmentIdCompartment->compNum == 116 ||
                    $order->compartmentIdCompartment->compNum == 121 ||
                    $order->compartmentIdCompartment->compNum == 126 ||
                    $order->compartmentIdCompartment->compNum == 131 ||
                    $order->compartmentIdCompartment->compNum == 136){
                    $numhcomp->rowsOccupied = 40;
                }else{
                    $numhcomp->rowsOccupied = 60;
                }
                $numhcomp->save();
            }else{
                if (date('Y-m-d', strtotime($numhcomps->freeDate))
                    < date('Y-m-d', strtotime( $cleaning." + 1 day"))){
                    $numhcomps->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                    $numhcomps->save();
                }
            }
            $order->numRows = $rowData[0][19];
            $order->plantingDistance = 50;
            $order->netNumOfPlantsF = floor($rowData2[0][8]);
            $order->netNumOfPlantsM = floor($rowData2[0][9]);
            $order->sowingF = floor($rowData2[0][10]);
            $order->sowingM = floor($rowData2[0][11]);
            $order->nurseryF = floor($rowData2[0][12]);
            $order->nurseryM = floor($rowData2[0][13]);
            if ($rowData2[0][14] == "Check!"){
                $order->check = $rowData2[0][14];
            }else{
                $order->check = "Great, no problem.";
            }

            $order->sowingDateM = date('Y-m-d', strtotime($sowF));
            $order->sowingDateF = date('Y-m-d', strtotime($sowM));
            $order->transplantingM= date('Y-m-d', strtotime($tM));
            $order->realisedNrOfPlantsM = $rowData2[0][18];
            $order->transplantingF = date('Y-m-d', strtotime($tF));
            $order->realisedNrOfPlantsF = floor($rowData2[0][20]);
            $order->pollinationF = date('Y-m-d', strtotime($polinF));
            $order->pollinationU = date('Y-m-d', strtotime($plinU));
            $order->harvestF = date('Y-m-d', strtotime($hF));
            $order->harvestU = date('Y-m-d', strtotime($hU));
            $order->pollenColectF = $order->harvestF;
            $order->pollenColectU = $order->harvestU;
            $order->steamDesinfectionF = $order->harvestU;
            $order->steamDesinfectionU = date('Y-m-d', strtotime($cleaning));
            $order->save();
            echo "Error: ";
            print_r($order->getErrors());
            echo "<br>";
        }
        echo "<br>";
    }

    /**
     * Displays a single Order model. view planting
     * @param integer $id
     * @return mixed
     */
    public function actionPlantingview($id)
    {
        return $this->render('planting/view', [
            'model' => $this->findModel($id),
        ]);
    }


    public function actionDateup()
    {
        $models = Order::find()->andFilterWhere(['<', 'numCrop', 13])->all();
        $con = 0;
        // crop = 10, comp id = 42, 2018, 3.5

        foreach ($models AS $model){
            $con++;
            $model->steamDesinfectionU = date('Y-m-d', strtotime("$model->steamDesinfectionF + 7 day"));
            if($model->save()){
            }else{
                echo $con." )";
                print_r($model->getErrors());
                echo "<br><br>";
            };
        }
    }

    public function  actionUpdateexc(){

        $inputFile = 'uploads/order5xlsx';

        ini_set('memory_limit','2048M');
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $orders = $objReader->load($inputFile)->getSheet(0);
        }catch (Exception $e){
            die('Error uploading the excel file');
        }
        // Poniendo el QUINTO excel

//        $orders = $objPHPExcel->getSheet(0);
        $highestRow = $orders->getHighestRow();
        $highestColumn = $orders->getHighestColumn();

        for ($row = 1; $row <= $highestRow; $row++) {

            $rowData = $orders->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData2 = $orders->rangeToArray('S' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
//323
            //482
            if ($row < 323 || $row > 606) {
                continue;
            }

            if ($rowData[0][0] > 12) {
                continue;
            } else if (($rowData[0][0] == 12) &&
                (
                    $rowData[0][18] == 132 ||
                    $rowData[0][18] == 134 ||
                    $rowData[0][18] == 136
                )) {
                continue;
            }



            $order = new Order();
            if ($rowData[0][2] == NULL){
                echo $rowData[0][2]." == NULL <br>";
                continue;
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }
            if (substr($hybrid, 2) > 999){
                $father = Father::findOne(['variety' =>$hybrid]);
                $mother = Mother::findOne(['variety' =>$hybrid]);

                $hybrid = new Hybrid();
                $hybrid->Crop_idcrops = 2;
                $hybrid->Father_idFather = $father->idFather;
                $hybrid->Mother_idMother = $mother->idMother;
                $hybrid->variety = $father->variety;
                $hybrid->save();
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }

            $hybrid = Hybrid::findOne(['variety' =>$hybrid]);
            $compartment = Compartment::findOne(['compNum' =>$rowData2[0][0]]);

            if ($rowData[0][7] == NULL ||
                $rowData[0][7] == "cancelled!" ||
                $rowData[0][7] == "cancelled" ||
                strpos($rowData[0][7], "-") ||
                $rowData[0][7] < 1000 ){
                $reqDelD = date("Y-m-d");
            }else{
                $reqDelD = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][7]));
            }
            if ($rowData[0][8] == NULL ||
                $rowData[0][8] == "cancelled!" ||
                $rowData[0][8] == "cancelled" ||
                strpos($rowData[0][8], "-")){
                $orderDate = date("Y-m-d");
            }else{
                $orderDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8]));
            }
            if ($rowData2[0][15] == NULL ||
                $rowData2[0][15] == "cancelled!" ||
                $rowData2[0][15] == "cancelled" ||
                strpos($rowData2[0][15], "-")){
                $sowM = date("Y-m-d");
            }else{
                $sowM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][15]));
            }
            if ($rowData2[0][16] == NULL ||
                $rowData2[0][16] == "cancelled!" ||
                $rowData2[0][16] == "cancelled" ||
                strpos($rowData2[0][16], "-")){
                $sowF = date("Y-m-d");
            }else{
                $sowF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][16]));
            }
            if ($rowData2[0][17] == NULL ||
                $rowData2[0][17] == "cancelled!" ||
                $rowData2[0][17] == "cancelled" ||
                strpos($rowData2[0][17], "-")){
                $tM = date("Y-m-d");
            }else{
                $tM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][17]));
            }
            if ($rowData2[0][19] == NULL ||
                $rowData2[0][19] == "cancelled!" ||
                $rowData2[0][19] == "cancelled" ||
                strpos($rowData2[0][19], "-")){
                $tF = date("Y-m-d");
            }else{
                $tF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][19]));
            }
            if ($rowData2[0][23] == NULL ||
                $rowData2[0][23] == "cancelled!" ||
                $rowData2[0][23] == "cancelled"){
                $polinF = date("Y-m-d");
            }else{
                $polinF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][23]));
            }
            if ($rowData2[0][26] == NULL ||
                $rowData2[0][26] == "cancelled!" ||
                $rowData2[0][26] == "cancelled" ||
                strpos($rowData2[0][26], "-")){
                $plinU = date("Y-m-d");
            }else{
                $plinU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][26]));
            }
            if ($rowData2[0][27] == NULL ||
                $rowData2[0][27] == "cancelled!" ||
                $rowData2[0][27] == "cancelled" ||
                strpos($rowData2[0][27], "-")){
                $hF = date("Y-m-d");
            }else{
                $hF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][27]));
            }
            if ($rowData2[0][30] == NULL ||
                $rowData2[0][30] == "cancelled!" ||
                $rowData2[0][30] == "cancelled" ||
                strpos($rowData2[0][30], "-")){
                $hU = date("Y-m-d");
            }else{
                $hU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][30]));
            }
            if ($rowData2[0][31] == NULL ||
                $rowData2[0][31] == "cancelled!" ||
                $rowData2[0][31] == "cancelled" ||
                strpos($rowData2[0][31], "-")){
                $cleaning = date("Y-m-d");
            }else{
                    $cleaning = date("Y-m-d", strtotime($hU . " + 7 day"));
            }

            if ($rowData2[0][0] == NULL || $rowData[0][0] == NULL){
                continue;
            }

            if ($rowData2[0][21] != NULL){
                $order->extractedPlantsF = floor($rowData2[0][21]);
            }

            if (empty($hybrid)){
                echo "Not found: ".$rowData[0][2]."<br>";
                continue;
            }else{
                //toda la asignación de valores;
                $order->Hybrid_idHybrid = $hybrid->idHybrid;
            }
            if($rowData[0][2] == NULL){
                continue;
            }
            $order->numCrop = $rowData[0][0];
            $order->orderKg = $rowData[0][4];
            $order->gpOrder = $rowData[0][5];
            $order->ReqDeliveryDate = $reqDelD;
            $order->orderDate = $orderDate;
            $order->contractNumber = $rowData[0][9];
            if (!empty($rowData[0][10])){
                if (!($rowData[0][10] =='yes')){
                    $order->ssRecDate = $rowData[0][10];
                }
            }
            $order->compartment_idCompartment = $compartment->idCompartment;
            $numhcomps = NumcropHasCompartment::findOne(['compartment_idCompartment' => $order->compartment_idCompartment, 'numcrop_cropnum' => $order->numCrop]);


            if (empty($numhcomps)){
                $numhcomp = new NumcropHasCompartment();
                $numhcomp->compartment_idCompartment = $order->compartment_idCompartment;
                $numhcomp->crop_idcrops = $order->hybridIdHybr->Crop_idcrops;
                $numhcomp->numcrop_cropnum = $order->numCrop;
                $numhcomp->createDate = date('Y-m-d');
                $numhcomp->lastUpdatedDate = date('Y-m-d');
                $numhcomp->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                $numhcomp->rowsLeft = 0;
                if ($order->compartmentIdCompartment->compNum == 111 ||
                    $order->compartmentIdCompartment->compNum == 116 ||
                    $order->compartmentIdCompartment->compNum == 121 ||
                    $order->compartmentIdCompartment->compNum == 126 ||
                    $order->compartmentIdCompartment->compNum == 131 ||
                    $order->compartmentIdCompartment->compNum == 136){
                    $numhcomp->rowsOccupied = 40;
                }else{
                    $numhcomp->rowsOccupied = 60;
                }
                $numhcomp->save();
            }else{
                if (date('Y-m-d', strtotime($numhcomps->freeDate))
                    < date('Y-m-d', strtotime( $cleaning." + 1 day"))){
                    $numhcomps->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                    $numhcomps->save();
                }
            }
            $order->numRows = $rowData[0][19];
            $order->plantingDistance = 50;
            $order->netNumOfPlantsF = floor($rowData2[0][8]);
            $order->netNumOfPlantsM = floor($rowData2[0][9]);
            $order->sowingF = floor($rowData2[0][10]);
            $order->sowingM = floor($rowData2[0][11]);
            $order->nurseryF = round(($order->netNumOfPlantsF) * 1.15);
            $order->nurseryM = round(($order->netNumOfPlantsM) * 1.15);
            if ($rowData2[0][14] == "Check!"){
                $order->check = $rowData2[0][14];
            }else{
                $order->check = "Great, no problem.";
            }

            $order->sowingDateM = date('Y-m-d', strtotime($sowM));
            $order->sowingDateF = date('Y-m-d', strtotime($sowF));

            $order->transplantingM= date('Y-m-d', strtotime($tM));
            $order->realisedNrOfPlantsM = $rowData2[0][18];
            $order->transplantingF = date('Y-m-d', strtotime($tF));
            $order->realisedNrOfPlantsF = floor($rowData2[0][20]);
            $order->pollinationF = date('Y-m-d', strtotime($polinF));
            $order->pollinationU = date('Y-m-d', strtotime($plinU));
            $order->harvestF = date('Y-m-d', strtotime($hF));
            $order->harvestU = date('Y-m-d', strtotime($hU));
            $order->pollenColectF = $order->harvestF;
            $order->pollenColectU = $order->harvestU;
            $order->steamDesinfectionF = $order->harvestU;
            $order->steamDesinfectionU = date('Y-m-d', strtotime($cleaning));

            $order->save();
            if ($order->getErrors()){
                echo $row . ") ";
                echo "Error: ";
                print_r($order->getErrors());
                echo "<br>";
            }
        }
    }


    public function actionImportExcelOrders14()
    {

        // Consiguiendo el archivo:


        $inputFile = 'uploads/order6.xlsx';
        ini_set('memory_limit','2048M');

        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
        }catch (Exception $e){
            die('Error uploading the excel file');
        }

        // Poniendo el QUINTO excel

        $orders = $objPHPExcel->getSheet(0);
        $highestRow = $orders->getHighestRow();
        $highestColumn = $orders->getHighestColumn();

        echo "<br>";
        for ($row = 1; $row <= $highestRow; $row++) {
            if ($row < 5) {
                continue;
            }

            $rowData = $orders->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData2 = $orders->rangeToArray('S' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            echo $row . ") ";

            if ($rowData[0][0] != 14) {
                continue;
            }


            if ($row == 498){
                echo $rowData[0][8];
//                die;
            }



            $order = new Order();
            if ($rowData[0][2] == NULL){
                echo $rowData[0][2]." == NULL <br>";
                continue;
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }
            if (substr($hybrid, 2) > 999){
                $father = Father::findOne(['variety' =>$hybrid]);
                $mother = Mother::findOne(['variety' =>$hybrid]);

                $hybrid = new Hybrid();
                $hybrid->Crop_idcrops = 2;
                $hybrid->Father_idFather = $father->idFather;
                $hybrid->Mother_idMother = $mother->idMother;
                $hybrid->variety = $father->variety;
                $hybrid->save();
            }
            $hybrid = $rowData[0][2];
            if (strpos($hybrid, "-")){
                $hybrids = explode('-', $hybrid);
                $hybrid = $hybrids[0];
            }

            $hybrid = Hybrid::findOne(['variety' =>$hybrid]);
            $compartment = Compartment::findOne(['compNum' =>$rowData2[0][0]]);

            if ($rowData[0][7] == NULL ||
                $rowData[0][7] == "cancelled!" ||
                $rowData[0][7] == "cancelled" ||
                strpos($rowData[0][7], "-") ||
                $rowData[0][7] < 1000 ){
                $reqDelD = date("Y-m-d");
            }else{
                $reqDelD = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][7]));
            }
            if ($rowData[0][8] == NULL ||
                $rowData[0][8] == "cancelled!" ||
                $rowData[0][8] == "cancelled" ||
                strpos($rowData[0][8], "-") ||
                $rowData[0][8] < 1000 ){
                $orderDate = date("Y-m-d");
            }else{
                $orderDate = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8]));
            }
            if ($rowData2[0][13] == NULL ||
                $rowData2[0][13] == "cancelled!" ||
                $rowData2[0][13] == "cancelled" ||
                strpos($rowData2[0][13], "-") ||
                $rowData2[0][13] < 1000 ){
                $sowM = date("Y-m-d");
            }else{
                $sowM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][13]));
            }
            if ($rowData2[0][14] == NULL ||
                $rowData2[0][14] == "cancelled!" ||
                $rowData2[0][14] == "cancelled" ||
                strpos($rowData2[0][14], "-") ||
                $rowData2[0][14] < 1000 ){
                $sowF = date("Y-m-d");
            }else{
                $sowF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][14]));
            }
            if ($rowData2[0][15] == NULL ||
                $rowData2[0][15] == "cancelled!" ||
                $rowData2[0][15] == "cancelled" ||
                strpos($rowData2[0][15], "-") ||
                $rowData2[0][15] < 1000 ){
                $tM = date("Y-m-d");
            }else{
                $tM = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][15]));
            }
            if ($rowData2[0][17] == NULL ||
                $rowData2[0][17] == "cancelled!" ||
                $rowData2[0][17] == "cancelled" ||
                strpos($rowData2[0][17], "-") ||
                $rowData2[0][17] < 1000 ){
                $tF = date("Y-m-d");
            }else{
                $tF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][17]));
            }
            if ($rowData2[0][21] == NULL ||
                $rowData2[0][21] == "cancelled!" ||
                $rowData2[0][21] == "cancelled" ||
                strpos($rowData2[0][21], "-") ||
                $rowData2[0][21] < 1000 ){
                $polinF = date("Y-m-d");
            }else{
                $polinF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][21]));
            }
            if ($rowData2[0][22] == NULL ||
                $rowData2[0][22] == "cancelled!" ||
                $rowData2[0][22] == "cancelled" ||
                strpos($rowData2[0][22], "-") ||
                $rowData2[0][22] < 1000 ){
                $plinU = date("Y-m-d");
            }else{
                $plinU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][22]));
            }
            if ($rowData2[0][23] == NULL ||
                $rowData2[0][23] == "cancelled!" ||
                $rowData2[0][23] == "cancelled" ||
                strpos($rowData2[0][23], "-") ||
                $rowData2[0][23] < 1000 ){
                $hF = date("Y-m-d");
            }else{
                $hF = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][23]));
            }
            if ($rowData2[0][24] == NULL ||
                $rowData2[0][24] == "cancelled!" ||
                $rowData2[0][24] == "cancelled" ||
                strpos($rowData2[0][24], "-") ||
                $rowData2[0][24] < 1000 ){
                $hU = date("Y-m-d");
            }else{
                $hU = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][24]));
            }
            if ($rowData2[0][25] == NULL ||
                $rowData2[0][25] == "cancelled!" ||
                $rowData2[0][25] == "cancelled" ||
                strpos($rowData2[0][25], "-") ||
                $rowData2[0][25] < 1000 ){
                $cleaning = date("Y-m-d");
            }else{
                $cleaning = date("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($rowData2[0][25]));
            }

            if ($rowData2[0][0] == NULL || $rowData[0][0] == NULL){
                continue;
            }

            if ($rowData2[0][21] != NULL){
                $order->extractedPlantsF = floor($rowData2[0][21]);
            }

            if (empty($hybrid)){
                echo "Not found: ".$rowData[0][2]."<br>";
                continue;
            }else{
                //toda la asignación de valores;
                $order->Hybrid_idHybrid = $hybrid->idHybrid;
            }
            if($rowData[0][2] == NULL){
                continue;
            }
            $order->numCrop = $rowData[0][0];
            $order->orderKg = $rowData[0][4];
            $order->gpOrder = $rowData[0][5];
            $order->ReqDeliveryDate = $reqDelD;
            $order->orderDate = $orderDate;
            $order->contractNumber = $rowData[0][9];
            if (!empty($rowData[0][10])){
                if (!($rowData[0][10] =='yes')){
                    $order->ssRecDate = $rowData[0][10];
                }
            }
            $order->compartment_idCompartment = $compartment->idCompartment;
            $numhcomps = NumcropHasCompartment::findOne(['compartment_idCompartment' => $order->compartment_idCompartment, 'numcrop_cropnum' => $order->numCrop]);


            if (empty($numhcomps)){
                $numhcomp = new NumcropHasCompartment();
                $numhcomp->compartment_idCompartment = $order->compartment_idCompartment;
                $numhcomp->crop_idcrops = $order->hybridIdHybr->Crop_idcrops;
                $numhcomp->numcrop_cropnum = $order->numCrop;
                $numhcomp->createDate = date('Y-m-d');
                $numhcomp->lastUpdatedDate = date('Y-m-d');
                $numhcomp->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                $numhcomp->rowsLeft = 0;
                if ($order->compartmentIdCompartment->compNum == 111 ||
                    $order->compartmentIdCompartment->compNum == 116 ||
                    $order->compartmentIdCompartment->compNum == 121 ||
                    $order->compartmentIdCompartment->compNum == 126 ||
                    $order->compartmentIdCompartment->compNum == 131 ||
                    $order->compartmentIdCompartment->compNum == 136){
                    $numhcomp->rowsOccupied = 40;
                }else{
                    $numhcomp->rowsOccupied = 60;
                }
                $numhcomp->save();
            }else{
                if (date('Y-m-d', strtotime($numhcomps->freeDate))
                    < date('Y-m-d', strtotime( $cleaning." + 1 day"))){
                    $numhcomps->freeDate = date('Y-m-d', strtotime( $cleaning." + 1 day"));
                    $numhcomps->save();
                }
            }
            $order->numRows = $rowData[0][19];
            $order->plantingDistance = 50;
            $order->netNumOfPlantsF = floor($rowData2[0][8]);
            $order->netNumOfPlantsM = floor($rowData2[0][9]);
            $order->sowingF = floor($rowData2[0][10]);
            $order->sowingM = floor($rowData2[0][11]);
            $order->nurseryF = floor($rowData2[0][12]);
            $order->nurseryM = floor($rowData2[0][13]);
            if ($rowData2[0][14] == "Check!"){
                $order->check = $rowData2[0][14];
            }else{
                $order->check = "Great, no problem.";
            }

            $order->sowingDateM = date('Y-m-d', strtotime($sowF));
            $order->sowingDateF = date('Y-m-d', strtotime($sowM));
            $order->transplantingM= date('Y-m-d', strtotime($tM));
            $order->realisedNrOfPlantsM = $rowData2[0][18];
            $order->transplantingF = date('Y-m-d', strtotime($tF));
            $order->realisedNrOfPlantsF = floor($rowData2[0][20]);
            $order->pollinationF = date('Y-m-d', strtotime($polinF));
            $order->pollinationU = date('Y-m-d', strtotime($plinU));
            $order->harvestF = date('Y-m-d', strtotime($hF));
            $order->harvestU = date('Y-m-d', strtotime($hU));
            $order->pollenColectF = $order->harvestF;
            $order->pollenColectU = $order->harvestU;
            $order->steamDesinfectionF = $order->harvestU;
            $order->steamDesinfectionU = date('Y-m-d', strtotime($cleaning));
            $order->save();
            echo "Error: ";
            print_r($order->getErrors());
            echo "<br>";
        }
    }


}
