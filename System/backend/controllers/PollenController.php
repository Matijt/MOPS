<?php

namespace backend\controllers;

use backend\models\Compartment;
use backend\models\Hybrid;
use backend\models\Order;
use Yii;
use backend\models\Pollen;
use backend\models\PollenSearch;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Model;
use yii\web\UploadedFile;
use yii2mod\rbac\filters\AccessControl;

/**
 * PollenController implements the CRUD actions for Pollen model.
 */
class PollenController extends Controller
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
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['Administrator'],
                    ],
                    [
                        'actions' => ['index', 'view',
                        ],
                        'allow' => true,
                        'roles' => ['Viewer', 'Administrator', 'Holland'],
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
     * Lists all Pollen models.
     * @return mixed
     */
    public function actionIndex()
    {

        $model = new Pollen();
        $searchModel = new PollenSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post())) {
            echo "hola";
            $model->file = UploadedFile::getInstance($model,'file');
            if ($model->file){
                $model->file->saveAs('uploads/pollen.xlsx');
                return $this->redirect('index.php?r=pollen%2Fimport-excel');
            }
        }

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionImportExcel(){

        $inputFile = 'uploads/pollen.xlsx';
        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPEXCEL = $objReader-> load($inputFile);
        }catch (Exception $e){die('Error');}

        $sheet = $objPHPEXCEL->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 1; $row <= $highestRow; $row++){
            if($row < 4){
                continue;
            }
            $rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row, NULL, TRUE, FALSE);

            $compartment = $rowData[0][0];
            $crop = $rowData[0][1];
            $hybrid = $rowData[0][2];
            $lotNumber = $rowData[0][4];

            $idHybrid = Hybrid::find()->andFilterWhere(['=' ,'variety', $hybrid])->one();
            if($idHybrid){
                $idHybrid = $idHybrid->attributes['idHybrid'];
            }else{
                continue;
            };
            $idCompartment = Compartment::find()->andFilterWhere(['=' ,'compNum', $compartment])->one();
            if($idCompartment){
                $idCompartment = $idCompartment->attributes['idCompartment'];
            }else{
                continue;
            };

            $order = Order::find()->
            andFilterWhere(['=', 'compartment_idCompartment', $idCompartment])
            ->andFilterWhere(['=', 'numCrop', $crop])
            ->andFilterWhere(['=', 'Hybrid_idHybrid', $idHybrid])
            ->andFilterWhere(['=', 'contractNumber', $lotNumber])->one();
            if($order) {
                $order = $order->attributes['idorder'];
            }else{
                continue;
            }

            $pollen = new Pollen();

            $pollen->order_idorder = $order;
            $pollen->harvestWeek = $rowData[0][5];
            $timestamp = \PHPExcel_Shared_Date::ExcelToPHP($rowData[0][6]);
            $pollen->harvestDate = date('Y-m-d', $timestamp);
            $pollen->harvestMl = $rowData[0][7];
            $pollen->useWeek = $rowData[0][8];
            $pollen->useMl = $rowData[0][9];
            $pollen->save();
        }
        $searchModel = new PollenSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        echo "<script>window.history.back();</script>";
    }

    /**
     * Displays a single Pollen model.
     * @param integer $idpollen
     * @param integer $order_idorder
     * @return mixed
     */
    public function actionView($idpollen, $order_idorder)
    {
        $searchModel = new PollenSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['=', 'order_idorder', $order_idorder]);
        return $this->render('view', [
            'model' => $this->findModel($idpollen, $order_idorder),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Pollen model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pollen();
        $modelspollen = [New Pollen()];

        if ($model->load(Yii::$app->request->post())) {
            $modelspollen = Model::createMultiple(Pollen::classname());
            Model::loadMultiple($modelspollen, Yii::$app->request->post());

            // validate all models
            $valid = Model::validateMultiple($modelspollen);

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                        foreach ($modelspollen as $modelpollen )
                        {
                            if($modelpollen->harvestDate) {
                                $modelpollen->harvestDate = date('Y-m-d', strtotime($modelpollen->harvestDate));
                            }
                            if($modelpollen->useWeek) {
                                $modelpollen->useWeek = date('Y-m-d', strtotime($modelpollen->useWeek));
                            }
                            if (! ($modelpollen->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        $transaction->commit();
                    echo "<script>window.history.back();</script>";
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
                'modelspollen' => (empty($modelspollen)) ? [new Pollen()] : $modelspollen
            ]);
        }
    }

    /**
     * Updates an existing Pollen model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $idpollen
     * @param integer $order_idorder
     * @return mixed
     */
    public function actionUpdate($idpollen, $order_idorder)
    {
        $model = $this->findModel($idpollen, $order_idorder);

        if ($model->load(Yii::$app->request->post())) {

            $model->harvestDate = date('Y-m-d', strtotime($model->harvestDate));

            if($model->save()) {
                $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                if (!(strpos($actual_link, 'page') !== false)){
                    $edit = "index.php?r=pollen%2Findex";
                }else{
                    $edit = 'index.php?r=pollen%2Findex'."&page=".$_GET['page'];
                }
                return $this->redirect($edit);
            }else{
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

    /**
     * Deletes an existing Pollen model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $idpollen
     * @param integer $order_idorder
     * @return mixed
     */
    public function actionDelete($idpollen, $order_idorder)
    {
        $this->findModel($idpollen, $order_idorder)->delete();

        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if (!(strpos($actual_link, 'page') !== false)) {
            $edit = "index.php?r=pollen%2Findex";
        } else {
            $edit = 'index.php?r=pollen%2Findex' . "&page=" . $_GET['page'];
        }
        return $this->redirect($edit);
    }

    /**
     * Finds the Pollen model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $idpollen
     * @param integer $order_idorder
     * @return Pollen the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($idpollen, $order_idorder)
    {
        if (($model = Pollen::findOne(['idpollen' => $idpollen, 'order_idorder' => $order_idorder])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
