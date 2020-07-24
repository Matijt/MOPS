<?php

namespace backend\controllers;

use backend\codigo\Facil;
use backend\models\Model;
use backend\models\Numcrop;
use backend\models\NumcropHasCompartment;
use backend\models\Order;
use Yii;
use backend\models\Trial;
use backend\models\TrialSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii2mod\rbac\filters\AccessControl;

/**
 * TrialController implements the CRUD actions for Trial model.
 */
class TrialController extends Controller
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
     * Lists all Trial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['>', 'id_trial', 1 ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Trial model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $modelO = Order::find()->andFilterWhere(['=', 'trial_id', $id])->all();

        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
            'modelO'=> $modelO
        ]);
    }

    /**
     * Creates a new Trial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Trial();
        $modelsorder = [New Order()];

        if ($model->load(Yii::$app->request->post())) {
            $modelsorder = Model::createMultiple(Order::classname());
            Model::loadMultiple($modelsorder, Yii::$app->request->post());

            // validate all models
//            $valid = Model::validateMultiple($modelsorder);

                $transaction = \Yii::$app->db->beginTransaction();
                try {

                        $connection = Yii::$app->getDb();
                        $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
                        $query = $command->queryAll();
                        $actualcrop = ArrayHelper::getValue($query, '0');
                        $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
                        if (!isset($actualcrop)) {
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

                        if (0 == ($rowsL - $model->numRows)) {
                            if (isset($rowsL)) {
                                $actualcrop = $actualcrop + 1;
                                if ($actualcrop > $maxCrop) {
                                    $modelNum = new Numcrop();
                                    $modelNum->save();
                                }
                                $model->numCrop = $actualcrop - 1;
                            }
                            $modelNC = new NumcropHasCompartment();
                            $modelNC->createDate = date('Y-m-d');
                            $modelNC->rowsOccupied = 0;
                            $modelNC->rowsLeft = ($model->compartmentIdCompartment->rowsNum);
                            $modelNC->compartment_idCompartment = $model->compartment_idCompartment;
                            $modelNC->numcrop_cropnum = $actualcrop;
                            $modelNC->crop_idcrops = 1;

                            $modelNC->save();

                            $modelOld = NumcropHasCompartment::findOne(['numcrop_cropnum' => ($actualcrop - 1), 'compartment_idCompartment' => $model->compartment_idCompartment]);
                            $modelOld->rowsLeft = new \stdClass();
                            $modelOld->rowsLeft = 0;
                            $modelOld->rowsOccupied = $model->compartmentIdCompartment->rowsNum;

                            $modelOld->save();
                            print_r($modelOld->getErrors());
                            print_r($modelNC->getErrors());

                        } else {
                            $has = NumcropHasCompartment::findOne(['numcrop_cropnum' => $actualcrop, 'compartment_idCompartment' => $model->compartment_idCompartment]);
                            if ($has) {
                                $has->rowsOccupied = $has->rowsOccupied + $model->numRows;
                                $has->rowsLeft = $has->rowsLeft - $model->numRows;
                                $has->save();
                            }
                        }


                    // Seguimos con el Array
                    if (!($model->save(false))) {
                        print_r($model->getErrors[]);
                        $transaction->rollBack();
                        die;
                    }else{
                        foreach ($modelsorder as $modelorder ) {
                            // Instrucciones de la orden
                            $modelorder->orderKg = 1;
                            $modelorder->gpOrder = 1;
                            $modelorder->contractNumber = 111111;
                            $modelorder->compartment_idCompartment = $model->compartment_idCompartment;
                            $facil = new Facil();
                            $facil->crear($modelorder);
                            $modelorder->numRows = $model->numRows;
                            $modelorder->trial_id = $model->id_trial;


                            // Seguimos con el Array
                            if (!($modelorder->save(false))) {
                                print_r($modelorder->getErrors[]);
                                $transaction->rollBack();
                                die;
                                break;
                            }
                        }
                    }

                    $transaction->commit();

                    echo "<script>window.history.back();</script>";
                } catch (Exception $e) {
                    $transaction->rollBack();
                }

            echo "<script>window.history.back();</script>";
        }else{
            return $this->renderAjax('create', [
                'model' => $model,
                'modelsorder' => (empty($modelsorder)) ? [new Order()] : $modelsorder
            ]);
        }
    }

    /**
     * Updates an existing Trial model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $models = $this->findModel($id);
        $modelsorder = Order::find()->andFilterWhere(['=', 'trial_id', $model->id_trial])->all();

        if ($model->load(Yii::$app->request->post())) {

            $modelsordern = Model::createMultiple(Order::classname());
            Model::loadMultiple($modelsordern, Yii::$app->request->post());

            $transaction = \Yii::$app->db->beginTransaction();
            try {

                $connection = Yii::$app->getDb();
                $actualcrop = $model->numCrop;
                $diferencia = $model->numRows - $models->numRows;



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

                if (0 == ($rowsL - $diferencia)) {
                    if (isset($rowsL)) {
                        $actualcrop = $actualcrop + 1;
                        if ($actualcrop > $maxCrop) {
                            $modelNum = new Numcrop();
                            $modelNum->save();
                        }
                        $model->numCrop = $actualcrop - 1;
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

                    $modelOld = NumcropHasCompartment::findOne(['numcrop_cropnum' => ($actualcrop - 1), 'compartment_idCompartment' => $model->compartment_idCompartment]);
                    $modelOld->rowsLeft = new \stdClass();
                    $modelOld->rowsLeft = 0;
                    $modelOld->rowsOccupied = $model->compartmentIdCompartment->rowsNum;

                    $modelOld->save();
                    print_r($modelOld->getErrors());
                    print_r($modelNC->getErrors());

                } else {
                    $has = NumcropHasCompartment::findOne(['numcrop_cropnum' => $actualcrop, 'compartment_idCompartment' => $model->compartment_idCompartment]);
                    if ($has) {
                        $has->rowsOccupied = $has->rowsOccupied + $diferencia;
                        $has->rowsLeft = $has->rowsLeft - $diferencia;
                        $has->save();
                    }
                }


                // Seguimos con el Array
                if (!($model->save(false))) {
                    print_r($model->getErrors[]);
                    $transaction->rollBack();
                    die;
                }else{
                    foreach ($modelsorder AS $i => $modelorder){
                        $modelorder->delete();
                    }
                    foreach ($modelsordern as $i => $modelorder ) {
                        // Instrucciones de la orden
                        $modelorder->orderKg = 1;
                        $modelorder->gpOrder = 1;
                        $modelorder->contractNumber = 111111;
                        $modelorder->compartment_idCompartment = $model->compartment_idCompartment;
                        // Asignar los valores que se cambian en el momento de crear.
                        if (
                            $modelorder->realisedNrOfPlantsF > 0 && $modelorder->extractedPlantsF > 0
                        ) {
                            $remainingPlantsF = $modelorder->realisedNrOfPlantsF - $modelorder->extractedPlantsF;
                        }
                        if (
                            $modelorder->realisedNrOfPlantsM > 0 && $modelorder->extractedPlantsM > 0
                        ) {
                            $remainingPlantsM = $modelorder->realisedNrOfPlantsM - $modelorder->extractedPlantsM;
                        }

                        $sowF = date('Y-m-d', strtotime("$modelorder->sowingDateF"));
                        $transplantingM = date('Y-m-d', strtotime("$modelorder->transplantingM"));
                        $transplantingF = date('Y-m-d', strtotime("$modelorder->transplantingF"));
                        $pollenColectF = date('Y-m-d', strtotime("$modelorder->pollenColectF"));
                        $pollenColectU = date('Y-m-d', strtotime("$modelorder->pollenColectU"));
                        $pollinationF = date('Y-m-d', strtotime("$modelorder->pollinationF"));
                        $pollinationU = date('Y-m-d', strtotime("$modelorder->pollinationU"));
                        $harvestF = date('Y-m-d', strtotime("$modelorder->harvestF"));
                        $harvestU = date('Y-m-d', strtotime("$modelorder->harvestU"));
                        $steamDesinfectionF = date('Y-m-d', strtotime("$modelorder->steamDesinfectionF"));
                        $steamDesinfectionU = date('Y-m-d', strtotime("$modelorder->steamDesinfectionU"));
                        $remarks = $modelorder->remarks;

                        // Crear
                        $facil = new Facil();
                        $facil->crear($modelorder);
                        $modelorder->numRows = $model->numRows;
                        $modelorder->trial_id = $model->id_trial;

                        // Reasignar los valores previamente seteados.
                        if (isset($remainingPlantsM)){$modelorder->remainingPlantsM = $remainingPlantsM;}
                        if (isset($remainingPlantsF)){$modelorder->remainingPlantsF = $remainingPlantsF;}
                        $modelorder->sowingDateF = $sowF;
                        $modelorder->transplantingM = $transplantingM;
                        $modelorder->transplantingF = $transplantingF;
                        $modelorder->pollenColectF = $pollenColectF;
                        $modelorder->pollenColectU = $pollenColectU;
                        $modelorder->pollinationF = $pollinationF;
                        $modelorder->pollinationU = $pollinationU;
                        $modelorder->harvestF = $harvestF;
                        $modelorder->harvestU = $harvestU;
                        $modelorder->steamDesinfectionF = $steamDesinfectionF;
                        $modelorder->steamDesinfectionU = $steamDesinfectionU;
                        $modelorder->remarks = $remarks;

                        // Seguimos con el Array
                        if (!($modelorder->save(false))) {
                            print_r($modelorder->getErrors[]);
                            $transaction->rollBack();
                            die;
                            break;
                        }
                    }
                }

                $transaction->commit();
                echo "<script>window.history.back();</script>";
                die;
            } catch (Exception $e) {
                $transaction->rollBack();
            }

            if ($model->save()) {
                echo "<script>window.history.back();</script>";
            }
        }

        return $this->renderAjax('update', [
            'model' => $model,
            'modelsorder' => (empty($modelsorder)) ? Order::find()->andFilterWhere(['=', 'trial_id', $model->id_trial])->all() : $modelsorder
        ]);
    }

    /**
     * Deletes an existing Trial model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $comphnumcrops = NumcropHasCompartment::find()
            ->andFilterWhere(['=', 'numcrop_cropnum', $this->findModel($id)->numCrop])
            ->andFilterWhere(['=', 'compartment_idCompartment', $this->findModel($id)->compartment_idCompartment])
            ->all();
        foreach ($comphnumcrops AS $comphnumcrop){
            $comphnumcrop->rowsOccupied = $comphnumcrop->rowsOccupied - $this->findModel($id)->numRows;
            $comphnumcrop->rowsLeft	= $comphnumcrop->rowsLeft + $this->findModel($id)->numRows;
            $comphnumcrop->save();
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Trial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Trial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Trial::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
