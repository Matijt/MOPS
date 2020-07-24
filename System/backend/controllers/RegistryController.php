<?php

namespace backend\controllers;

use backend\models\Estimations;
use backend\models\Order;
use backend\models\Stocklist;
use backend\models\StocklistHasOrder;
use Yii;
use backend\models\Registry;
use backend\models\RegistrySearch;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2mod\rbac\filters\AccessControl;
use backend\models\Model;

/**
 * RegistryController implements the CRUD actions for Registry model.
 */
class RegistryController extends Controller
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
                        'actions' => ['create', 'update', 'delete', 'orders', 'istomato', 'istomato2'],
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
     * Lists all Registry models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RegistrySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Registry model.
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
     * Creates a new Registry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Registry();
        $model = new Registry();
        $quantities = [New Registry()];

        if ($model->load(Yii::$app->request->post())) {
            if ($model->fruitsCount != 0 || $model->fruitsCount == null){
                $model->numRow = 0;
                $model->LUser = Yii::$app->user->identity->username;
                $model->save();
            }else{
                $quantities = Model::createMultiple(Registry::classname());
                Model::loadMultiple($quantities, Yii::$app->request->post());

                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    foreach ($quantities as $quantity) {
                        $quantity->order_idorder = $model->order_idorder;
                        if ($quantity->quantity == null || $quantity->numRow == null) {
                            continue;
                        }
                        if (!($quantity->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
            echo "<script>window.history.back();</script>";
            echo "<script>window.history.back();</script>";
            die;
        }

        return $this->renderAjax('create', [
            'model' => $model,
            'quantities' => (empty($quantities)) ? [new Registry()] : $quantities
        ]);
    }

    /**
     * Updates an existing Registry model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {


            $model->LUser = Yii::$app->user->identity->username;
			if(substr($model->orderIdorder->hybridIdHybr->variety, 0, 1) == "T"){
				$model->fruitsCount = 0;
			}else{
				$model->numRow = 0;
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
     * Deletes an existing Registry model.
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
     * Finds the Registry model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Registry the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Registry::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionOrders($comp)
    {
        $orders = \backend\models\Order::find()->joinWith('compartmentIdCompartment')->joinWith('hybridIdHybr')->orderBy('idorder')->andFilterWhere(['=', 'order.delete', 0])
            ->andFilterWhere(['>', 'order.steamDesinfectionU', date('Y-m-d')])
            ->andFilterWhere(['=', 'order.state', 'Active'])
            ->andFilterWhere(['=', 'order.compartment_idCompartment', $comp])
            ->andFilterWhere(['!=', 'order.sowingDateF', '1970-01-01'])
            ->andFilterWhere(['=', 'order.trial_id', 1])->all();

        foreach ($orders AS $order) {
            echo "<option value='" . $order->idorder . "'>" . $order->fullname . "</option>";
        }

    }

    function actionIstomato($id)
    {
        $orders = \backend\models\Order::find()
            ->joinWith('compartmentIdCompartment')->joinWith('hybridIdHybr')
               ->andFilterWhere(['=', 'order.idorder', $id])->all();

        foreach ($orders AS $order) {
            $res[0] = substr($order->hybridIdHybr->variety, 0, 1);

            if ($order->realisedNrOfPlantsF > 0){
                $N = $order->realisedNrOfPlantsF;
            }else{
                $N = $order->netNumOfPlantsF;
            }
        }
        $k = 1.65;
        $e = .1;
        $p = .5;
        $q = .5;
        $res[1] = round(($k*$k*$p*$q*$N)/(($e*$e*($N-1))+$k*$k*$p*$q));
        $res[2] = $N;
        $res[3] = 0;
        $res[4] = 0;

        if($id) {
            $order = Order::findOne($id);

            if(substr($order->hybridIdHybr->variety, 0, 1) == "T" && StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $id])->one() != null){
                $res[3] = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $id])->one()->totalInStock;
                $res[4] = StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $id])->one()->totalNumberOfFruitsHarvested;
            }else {
                $pastEstimations = Estimations::find()
                    ->joinWith('orderIdorder')
                    ->andFilterWhere(['=', 'order.Hybrid_idHybrid', $order->Hybrid_idHybrid])->all();

                $countPastEstimations1 = Estimations::find()
                    ->joinWith('orderIdorder')
                    ->andFilterWhere(['=', 'order.Hybrid_idHybrid', $order->Hybrid_idHybrid])
                    ->andFilterWhere(['!=', 'gramPerFruit', 0])
                    ->count();

                $countPastEstimations2 = Estimations::find()
                    ->joinWith('orderIdorder')
                    ->andFilterWhere(['=', 'order.Hybrid_idHybrid', $order->Hybrid_idHybrid])
                    ->andFilterWhere(['!=', 'gramPerFruit2', 0])
                    ->count();

                if ($countPastEstimations1 == 0) {
                    $countPastEstimations1 = 1;
                }
                if ($countPastEstimations2 == 0) {
                    $countPastEstimations2 = 1;
                }


                if ($pastEstimations != null) {
                    $GPF1 = 0;
                    $GPF2 = 0;
                    foreach ($pastEstimations AS $pastEstimation) {
                        $GPF1 = $GPF1 + $pastEstimation->gramPerFruit;
                        $GPF2 = $GPF2 + $pastEstimation->gramPerFruit2;
                    }
                    $GPF1 = $GPF1 / $countPastEstimations1;
                    $GPF2 = $GPF2 / $countPastEstimations2;


                    $res[3] = $GPF1;
                    $res[4] = $GPF2;
                }
            }
        }
        return $res[0].",".$res[1].",".$res[2].",".$res[3].",".$res[4];
    }

    function actionIstomato2($id, $date)
    {
    $date = date('Y-m-d', strtotime($date));

        $orders = \backend\models\Order::find()
            ->joinWith('compartmentIdCompartment')->joinWith('hybridIdHybr')
            ->andFilterWhere(['=', 'order.idorder', $id])->all();

        $res[3] = 0;
        $res[4] = 0;

        if($id) {
            $order = Order::findOne($id);

            if(substr($order->hybridIdHybr->variety, 0, 1) == "T" && StocklistHasOrder::find()->andFilterWhere(['=', 'order_idorder', $id])->one() != null){
                foreach(Stocklist::find()->andFilterWhere(['=', 'hasOrderId', $id])->andFilterWhere(['=', 'status', 'In Stock'])->andFilterWhere(['<', 'harvestDate', $date])->all() AS $stocklist){
                    $res[3] = $res[3]+$stocklist->drySeedWeight;
                    $res[4] = $res[4]+$stocklist->numberOfFruitsHarvested;
                }
            }else {
                $res[3] = 0;
                $res[4] = 0;
            }
        }
        return $res[3].",".$res[4];
    }
}
