<?php

namespace backend\controllers;

use backend\models\Father;
use backend\models\Mother;
use phpDocumentor\Reflection\Types\String_;
use Yii;
use backend\models\Hybrid;
use backend\models\HybridSearch;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2mod\rbac\filters\AccessControl;

/**
 * HybridController implements the CRUD actions for Hybrid model.
 */
class HybridController extends Controller
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
                        'roles' => ['Administrator', 'Production'],
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
     * Lists all Hybrid models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HybridSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['=', '`hybrid`.`delete`', '0'])
        ->andFilterWhere(['=', '`father`.`delete`', '0'])
        ->andFilterWhere(['=', '`mother`.`delete`', '0'])
        ->andFilterWhere(['=', '`crop`.`delete`', '0']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Hybrid model.
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
     * Creates a new Hybrid model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Hybrid();

        if ($model->load(Yii::$app->request->post())) {
            if($model->sowingFemale == null){
                $model->sowingFemale =0;
            }
            if($model->transplantingMale == null){
                $model->transplantingMale =0;
            }
            if($model->transplantingFemale == null){
                $model->transplantingFemale =0;
            }
            if($model->pollenColectF == null){
                $model->pollenColectF =0;
            }
            if($model->pollenColectU == null){
                $model->pollenColectU =0;
            }
            if($model->pollinitionF == null){
                $model->pollinitionF =0;
            }
            if($model->pollinitionU == null){
                $model->pollinitionU =0;
            }
            if($model->harvestF == null){
                $model->harvestF =0;
            }
            if($model->harvestU == null){
                $model->harvestU =0;
            }
            if($model->steamDesinfection == null){
                $model->steamDesinfection =0;
            }
            if($model->save()) {
                echo "<script>window.history.back();</script>";
            }else{
                return $this->renderAjax('create', [
                    'model' => $model,
                ]);
            }
        }
        else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Hybrid model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);


        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                echo "<script>window.history.back();</script>";
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
     * Deletes an existing Hybrid model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete = 1;
        $model->save();
        echo "<script>window.history.back();</script>";
    }

    /**
     * Finds the Hybrid model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Hybrid the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Hybrid::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionImportExcelParentals(){
        $inputFile = 'uploads/hybrids.xlsx';

        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
        }catch (Exception $e){
            die('Error uploading the excel file');
        }

        $parents = $objPHPExcel->getSheet(4);
        $highestRow = $parents->getHighestRow();
        $highestColumn = $parents->getHighestColumn();

        for ($row = 1; $row <= $highestRow; $row++){
            if ($row < 2){
                continue;
            }

            $rowData = $parents->rangeToArray('A'.$row.':'.$highestColumn.$row, NULL, TRUE, FALSE);
            $father = new Father();
            $mother = new Mother();
            $father->variety = $rowData[0][0];
            $mother->variety = $rowData[0][0];
            if($rowData[0][1] == "" || $rowData[0][1] == NULL){
                $father->steril = 100;
                $mother->steril = 100;
            }else{
                if (strpos($rowData[0][0], 'P') !== false && $rowData[0][1] != 'Fertile' ) {
                    $father->steril = 50;
                    $mother->steril = 50;
                }else{
                    $father->steril = 100;
                    $mother->steril = 100;
                }
            }

            $father->remarks = $rowData[0][1];
            $mother->remarks = $rowData[0][1];

            $mother->tsw = $rowData[0][2];
            $father->tsw = $rowData[0][2];

            $mother->germination = 100;
            $father->germination = 100;

            $mother->gP = 6;
            $mother->save();
            $father->save();


            echo $row."   ";
            echo $mother->variety;
            echo "<br>";
        }
    }

    public function actionImportExcelHybrids(){

        // Consiguiendo el archivo:

        $inputFile = 'uploads/hybrids.xlsx';

        try{
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
        }catch (Exception $e){
            die('Error uploading the excel file');
        }

        // Poniendo los tomates Rootstock

        $tomato = $objPHPExcel->getSheet(3);
        $highestRow = $tomato->getHighestRow();
        $highestColumn = $tomato->getHighestColumn();

        echo "TOMATOS ROOTSTOCK<br>";
        for ($row = 1; $row <= $highestRow; $row++){
            if ($row < 3){
                continue;
            }

            $rowData = $tomato->rangeToArray('A'.$row.':'.$highestColumn.$row, NULL, TRUE, FALSE);
            $hybrid = new Hybrid();

            $hybrid->variety = $rowData[0][0];
            $mother = Mother::findOne(['variety' =>$rowData[0][1]]);
            $father = Father::findOne(['variety' =>$rowData[0][2]]);
            $hybrid->tsw= $rowData[0][3];
            $hybrid->Crop_idcrops = 2;

            if (empty($mother) || empty($father)){
                if (empty($mother)){
                    echo $rowData[0][1]."<br>";
                }
                if (empty($father)){

                    echo $rowData[0][2]."<br>";
                }
                continue;
            }else{
                //toda la asignación de valores;
                $hybrid->Father_idFather = $father->idFather;
                $hybrid->Mother_idMother = $mother->idMother;
            }
            $hybrid->save();
        }
        echo "<br>";
        // Poniendo las berenjenas

        $egp= $objPHPExcel->getSheet(2);
        $highestRow = $egp->getHighestRow();
        $highestColumn = $egp->getHighestColumn();

        echo "EGGPLANTS<br>";
        for ($row = 1; $row <= $highestRow; $row++){
            if ($row < 3){
                continue;
            }

            $rowData = $egp->rangeToArray('A'.$row.':'.$highestColumn.$row, NULL, TRUE, FALSE);
            $hybrid = new Hybrid();

            $hybrid->variety = $rowData[0][0];
            $mother = Mother::findOne(['variety' =>$rowData[0][1]]);
            $father = Father::findOne(['variety' =>$rowData[0][2]]);
            $hybrid->tsw= 5;
            $hybrid->Crop_idcrops = 2;

            if (empty($mother) || empty($father)){
                if (empty($mother)){
                    echo $rowData[0][1]."<br>";
                }
                if (empty($father)){

                    echo $rowData[0][2]."<br>";
                }
                continue;
            }else{
                //toda la asignación de valores;
                $hybrid->Father_idFather = $father->idFather;
                $hybrid->Mother_idMother = $mother->idMother;
            }
            $hybrid->save();
        }
        echo "<br>";
        // Poniendo los pimientos

        $egp= $objPHPExcel->getSheet(1);
        $highestRow = $egp->getHighestRow();
        $highestColumn = $egp->getHighestColumn();

        echo "PEPPERS<br>";
        for ($row = 1; $row <= $highestRow; $row++){
            if ($row < 3){
                continue;
            }

            $rowData = $egp->rangeToArray('A'.$row.':'.$highestColumn.$row, NULL, TRUE, FALSE);
            $hybrid = new Hybrid();

            $hybrid->variety = $rowData[0][0];
            $mother = Mother::findOne(['variety' =>$rowData[0][1]]);
            $father = Father::findOne(['variety' =>$rowData[0][2]]);
            $hybrid->tsw= $rowData[0][3];
            $hybrid->Crop_idcrops = 2;

            if (empty($mother) || empty($father)){
                if (empty($mother)){
                    echo $rowData[0][1]."<br>";
                }
                if (empty($father)){
                    echo $rowData[0][2]."<br>";
                }
                continue;
            }else{
                //toda la asignación de valores;
                $hybrid->Father_idFather = $father->idFather;
                $hybrid->Mother_idMother = $mother->idMother;
            }
            $hybrid->save();
        }
        echo "<br>";
        // Poniendo los tomates

        $egp= $objPHPExcel->getSheet(0);
        $highestRow = $egp->getHighestRow();
        $highestColumn = $egp->getHighestColumn();

        echo "TOMATOS<br>";
        for ($row = 1; $row <= $highestRow; $row++){
            if ($row < 3){
                continue;
            }

            $rowData = $egp->rangeToArray('A'.$row.':'.$highestColumn.$row, NULL, TRUE, FALSE);
            $hybrid = new Hybrid();

            $hybrid->variety = $rowData[0][0];
            $mother = Mother::findOne(['variety' =>$rowData[0][1]]);
            $father = Father::findOne(['variety' =>$rowData[0][2]]);
            $hybrid->tsw= $rowData[0][3];
            $hybrid->Crop_idcrops = 2;

            if (empty($mother) || empty($father)){
                if (empty($mother)){
                    echo $rowData[0][1]."<br>";
                }
                if (empty($father)){

                    echo $rowData[0][2]."<br>";
                }
                continue;
            }else{
                //toda la asignación de valores;
                $hybrid->Father_idFather = $father->idFather;
                $hybrid->Mother_idMother = $mother->idMother;
            }
            $hybrid->save();
        }
    }
}
