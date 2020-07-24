<?php

namespace backend\controllers;

use Yii;
use backend\models\Father;
use backend\models\FatherSearch;
use backend\models\Germination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2mod\rbac\filters\AccessControl;

/**
 * FatherController implements the CRUD actions for Father model.
 */
class FatherController extends Controller
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
     * Lists all Father models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FatherSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['=', 'delete', '0']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Father model.
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
     * Creates a new Father model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Father();
        $germination = new Germination();

        if ($model->load(Yii::$app->request->post())) {
            $germination->maleOrfemale = "M";
            $germination->description = "Created Male: Germination percentage = ".$model->germination."% Created for the user: ".Yii::$app->user->identity->username;
            $germination->variety = $model->variety;
            if($model->save() && $germination->save()) {

                echo "<script>window.history.back();</script>";
            }else{
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

    /**
     * Updates an existing Father model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modeltest = $this->findModel($id);
        $germination = new Germination();

        if ($model->load(Yii::$app->request->post())) {
       //     $model->germination = round((($model->germination)+($modeltest->germination))/2);
            $germination->maleOrfemale = "M";
            $germination->description = "Updated Male: Germination percentage = ".$model->germination."% Updated for the user: ".Yii::$app->user->identity->username;
            $germination->variety = $model->variety;
            if($model->save() && $germination->save()){
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
     * Deletes an existing Father model.
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
     * Finds the Father model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Father the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Father::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
