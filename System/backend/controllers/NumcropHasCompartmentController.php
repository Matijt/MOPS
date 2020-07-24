<?php

namespace backend\controllers;

use backend\models\Order;
use backend\models\OrderSearch;
use backend\models\Trial;
use Yii;
use backend\models\NumcropHasCompartment;
use backend\models\NumcropHasCompartmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii2mod\rbac\filters\AccessControl;

/**
 * NumcropHasCompartmentController implements the CRUD actions for NumcropHasCompartment model.
 */
class NumcropHasCompartmentController extends Controller
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
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['Administrator', 'Holland', 'Production'],
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
     * Lists all NumcropHasCompartment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NumcropHasCompartmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->addOrderBy('numcrop_cropnum DESC, crop_idcrops ASC');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NumcropHasCompartment model.
     * @param integer $numcrop_cropnum
     * @param integer $compartment_idCompartment
     * @return mixed
     */
    public function actionView($numcrop_cropnum, $compartment_idCompartment)
    {
        $modelO = Order::find()->andFilterWhere(['=', 'numCrop', $numcrop_cropnum])
        ->andFilterWhere(['=', 'compartment_idCompartment', $compartment_idCompartment])
        ->andFilterWhere(['=', 'trial_id', 1])
        ->andFilterWhere(['=', 'delete', 0])
        ->all();
        $modelT = Trial::find()
            ->andFilterWhere(['=', 'compartment_idCompartment', $compartment_idCompartment])
            ->andFilterWhere(['=', 'numCrop', $numcrop_cropnum])
            ->all();
        $model = $this->findModel($numcrop_cropnum, $compartment_idCompartment);

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = false;
        $dataProvider->query->andFilterWhere(['=', 'order.delete',0])
            ->andFilterWhere(['=', 'hybrid.delete', 0])
//            ->andFilterWhere(['>', 'order.steamDesinfectionU', date('Y-m-d')])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->andFilterWhere(['=', 'order.compartment_idCompartment', $compartment_idCompartment])
            ->andFilterWhere(['=', 'order.numCrop', $numcrop_cropnum])
            ->andFilterWhere(['!=', 'order.sowingDateF', '1970-01-01'])
            ->andFilterWhere(['=', 'order.trial_id', 1])
            ->all();

        return $this->renderAjax('view', [
            'model' => $model,
            'modelO' => $modelO,
            'modelT' => $modelT,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new NumcropHasCompartment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NumcropHasCompartment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            echo "<script>window.history.back();</script>";
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing NumcropHasCompartment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $numcrop_cropnum
     * @param integer $compartment_idCompartment
     * @return mixed
     */
    public function actionUpdate($numcrop_cropnum, $compartment_idCompartment)
    {
        $model = $this->findModel($numcrop_cropnum, $compartment_idCompartment);

        if ($model->load(Yii::$app->request->post()) ) {
            $modelOld = NumcropHasCompartment::findOne(['numcrop_cropnum' => (($model->numcrop_cropnum)-1), 'compartment_idCompartment' => $model->compartment_idCompartment]);
            if($modelOld) {
                $model->freeDate = date('Y-m-d', strtotime("$modelOld->freeDate  + " . ($model->cropIdcrops->durationOfTheCrop) . " day"));
            }else{
                $model->freeDate = date('Y-m-d');
            }
            $model->lastUpdatedDate = date('Y-m-d');

            if($model->cropIdcrops->crop == "Not planned"){
                $model->freeDate = null;
            }
            if ($model->estado == 'Inactive'){
                $model->crop_idcrops = 2;
                $info = Order::find()
                    ->andFilterWhere(['=', 'compartment_idCompartment', $model->compartment_idCompartment])
                    ->andFilterWhere(['=', 'numCrop', $model->numcrop_cropnum])->all();
                foreach ($info AS $q){
                    $q->selector = 'Inactive';
                    $q->state = 'Inactive';
                    $q->canceledDate = date('Y-m-d');
                    $q->save();
                }
                $order = Order::find()->where(['idorder' => $info[0]['idorder']])->one();
                $model->freeDate = date('Y-m-d', strtotime("$model->freeDate + " . ($order->hybridIdHybr->cropIdcrops->transplantingMale) . " day"));
                $crop = NumcropHasCompartment::find()
                    ->andFilterWhere(['=', 'compartment_idCompartment', $model->compartment_idCompartment])
                    ->andFilterWhere(['=', 'numcrop_cropnum', ($model->numcrop_cropnum+1)])
                    ->all();
                if(!$crop) {
                    $modelNC = new NumcropHasCompartment();
                    $modelNC->createDate = date('Y-m-d');
                    $modelNC->rowsOccupied = 0;
                    $modelNC->rowsLeft = ($model->compartmentIdCompartment->rowsNum);
                    $modelNC->compartment_idCompartment = $model->compartment_idCompartment;
                    $modelNC->numcrop_cropnum = ($model->numcrop_cropnum + 1);
                    $modelNC->crop_idcrops = 1;
                    $modelNC->save();
                }

                $model->rowsOccupied = $model->compartmentIdCompartment->rowsNum;
                $model->rowsLeft = 0;
            }else{
                $info = Order::find()
                    ->andFilterWhere(['=', 'compartment_idCompartment', $model->compartment_idCompartment])
                    ->andFilterWhere(['=', 'numCrop', $model->numcrop_cropnum])->all();
                foreach ($info AS $q){
                    $q->selector = 'Active';
                    $q->state = 'Active';
                    $q->save();
                }

            }
            if($model->save()) {
                Yii::$app->session->setFlash('warning', "Saved.");
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
     * Deletes an existing NumcropHasCompartment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $numcrop_cropnum
     * @param integer $compartment_idCompartment
     * @return mixed
     */
    public function actionDelete($numcrop_cropnum, $compartment_idCompartment)
    {
        $this->findModel($numcrop_cropnum, $compartment_idCompartment)->delete();

        echo "<script>window.history.back();</script>";
    }

    /**
     * Finds the NumcropHasCompartment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $numcrop_cropnum
     * @param integer $compartment_idCompartment
     * @return NumcropHasCompartment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($numcrop_cropnum, $compartment_idCompartment)
    {
        if (($model = NumcropHasCompartment::findOne(['numcrop_cropnum' => $numcrop_cropnum, 'compartment_idCompartment' => $compartment_idCompartment])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
