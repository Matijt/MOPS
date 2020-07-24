<?php

namespace backend\controllers;

use Yii;
use backend\models\Registrynursery;
use backend\models\RegistrynurserySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RegistrynurseryController implements the CRUD actions for Registrynursery model.
 */
class RegistrynurseryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Registrynursery models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RegistrynurserySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Registrynursery model.
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
     * Creates a new Registrynursery model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Registrynursery();

        if ($model->load(Yii::$app->request->post())) {
            // We change the dates's format
            $model->recDate = date('Y-m-d', strtotime($model->recDate));
            $model->trasplantCompartment = date('Y-m-d', strtotime($model->trasplantCompartment));

            if($model->FM == 'F'){
                $model->sowing = $model->orderIdorder->sowingDateF;
                $model->transplant = $model->orderIdorder->transplantingF;
            }else{
                $model->sowing = $model->orderIdorder->sowingDateM;
                $model->transplant = $model->orderIdorder->transplantingM;
            }

            // We need to get the plants per compartment first, using the information of the order based in the excel.
            $numrow = 0;
            if ($model->numRows == 0 || $model->numRows == null){
                if ($model->orderIdorder->numRowsOpt == null || $model->orderIdorder->numRowsOpt == null){
                    $numrow = $model->orderIdorder->numRows;
                }else{
                    $numrow = $model->orderIdorder->numRowsOpt;
                }
            }else{
                $numrow = $model->numRows;
            }
            $numPlants = 0;
            if ($model->numPlants == 0 || $model->numPlants == null){
                $numPlants = $model->orderIdorder->NumOfPlantsPerRow;
            }else{
                $numPlants = $model->numPlants;
            }
            $model->plantsPerCompartment = $numrow*$numPlants;

            // We get the seeds we are going to use, based on the excel.
            if ($model->orderIdorder->hybridIdHybr->motherIdMother->steril == 100){
                $model->seedsUsed = round((100/$model->usedGermination)*$model->plantsPerCompartment);
            }else{
                $model->seedsUsed = round(((100/$model->usedGermination)*$model->plantsPerCompartment)*2);
            }
            $model->seedsUsed = round($model->seedsUsed*1.2);
            $model->remain = round($model->realSeedsRecieved - $model->seedsUsed);

            // We get the needed quantities of Trays
            $model->trays = $model->seedsUsed/180;

            // We get the germination % of PROMEX
            if ($model->seedsReallyGerminated && $model->seedsUsed){
                $model->germinationReal = ($model->seedsReallyGerminated/$model->seedsUsed)*100;

                // We get the seeds that remain in tray
                $model->remainTray = round((($model->germinationReal/100)*$model->seedsUsed) - $model->plantsPerCompartment);
            }

            $model->save();
            echo "<script>window.history.back();</script>";
            die;
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Registrynursery model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // We change the dates's format
            $model->recDate = date('Y-m-d', strtotime($model->recDate));
            $model->sowing = date('Y-m-d', strtotime($model->sowing));
            $model->transplant = date('Y-m-d', strtotime($model->transplant));
            $model->trasplantCompartment = date('Y-m-d', strtotime($model->trasplantCompartment));

            // We need to get the plants per compartment first, using the information of the order based in the excel.
            $numrow = 0;
            if ($model->numRows == 0 || $model->numRows == null){
                if ($model->orderIdorder->numRowsOpt == null || $model->orderIdorder->numRowsOpt == null){
                    $numrow = $model->orderIdorder->numRows;
                }else{
                    $numrow = $model->orderIdorder->numRowsOpt;
                }
            }else{
                $numrow = $model->numRows;
            }
            $numPlants = 0;
            if ($model->numPlants == 0 || $model->numPlants == null){
                $numPlants = $model->orderIdorder->NumOfPlantsPerRow;
            }else{
                $numPlants = $model->numPlants;
            }
            $model->plantsPerCompartment = $numrow*$numPlants;

            // We get the seeds we are going to use, based on the excel.
            if ($model->orderIdorder->hybridIdHybr->motherIdMother->steril == 100){
                $model->seedsUsed = round((100/$model->usedGermination)*$model->plantsPerCompartment);
            }else{
                $model->seedsUsed = round(((100/$model->usedGermination)*$model->plantsPerCompartment)*2);
            }
            $model->seedsUsed = round($model->seedsUsed*1.2);
            $model->remain = round($model->realSeedsRecieved - $model->seedsUsed);

            // We get the needed quantities of Trays
            $model->trays = $model->seedsUsed/180;

            // We get the germination % of PROMEX
            if ($model->seedsReallyGerminated && $model->seedsUsed){
                $model->germinationReal = ($model->seedsReallyGerminated/$model->seedsUsed)*100;

                // We get the seeds that remain in tray
                $model->remainTray = round((($model->germinationReal/100)*$model->seedsUsed) - $model->plantsPerCompartment);
            }

            $model->save();
            echo "<script>window.history.back();</script>";
            die;
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Registrynursery model.
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
     * Finds the Registrynursery model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Registrynursery the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Registrynursery::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
