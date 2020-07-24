<?php

namespace backend\controllers;

use Yii;
use backend\models\Compartment;
use backend\models\CompartmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii2mod\rbac\filters\AccessControl;

/**
 * CompartmentController implements the CRUD actions for Compartment model.
 */
class CompartmentController extends Controller
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
                        'actions' => ['index'],
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
     * Lists all Compartment models.
     * @return mixed
     */
    public function actionIndex()
    {
         $searchModel = new CompartmentSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

    }

    /**
     * Displays a single Compartment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        if(Yii::$app->user->can('view-compartment')){
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }else{
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Creates a new Compartment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->user->can('create-compartment')){
            $model = new Compartment();

            if ($model->load(Yii::$app->request->post())) {

                $model->width = ($model->rowsNum)*0.8;
                $model->grossLength = 40.5;
                $model->netLength = 37.75;
                $model->netSurface = ($model->netLength)*($model->width);
                $model->grossSurface = ($model->grossLength) * ($model->width);

                echo $model->width."<br>";
                echo $model->grossLength."<br>";
                echo $model->netLength."<br>";
                echo $model->netSurface."<br>";
                echo $model->grossSurface."<br>";
                $model->save();
                return $this->redirect(['view', 'id' => $model->idCompartment]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }else{
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Updates an existing Compartment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        if(Yii::$app->user->can('update-compartment')){
            $model = $this->findModel($id);

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->idCompartment]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }else{
            throw new ForbiddenHttpException;
        }

    }


    public function actionList($id)
    {
        $councompartment = Compartment::find()
            ->where(['idCompartment' => $id])
            ->count();

        $compartment = Compartment::find()
            ->where(['idCompartment' => $id])
            ->all();
        if ($councompartment > 0){

            foreach ($compartment as $compartment) {
               echo "<option value='".$compartment->idCompartment."'>".$compartment->compNum."</option>";
            }
        }else{
            echo "<option>-</option>";
        }
    }

    /**
     * Deletes an existing Compartment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
            if(Yii::$app->user->can('delete-compartment')){

                $this->findModel($id)->delete();

                return $this->redirect(['index']);
        }else{
            throw new ForbiddenHttpException;
        }

    }

    /**
     * Finds the Compartment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Compartment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Compartment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionValidcompartment($id, $try)
    {

    }

}
