<?php

namespace backend\controllers;

use Yii;
use backend\models\History;
use backend\models\HistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HistoryController implements the CRUD actions for History model.
 */
class HistoryController extends Controller
{
    /**
     * @inheritdoc
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
     * Lists all History models.
     * @return mixed
     */
    public function actionIndex()
    {
           if(Yii::$app->user->can('view-history')){
                $searchModel = new HistorySearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }else{
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Displays a single History model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
           if(Yii::$app->user->can('view-history')){
                return $this->render('view', [
                    'model' => $this->findModel($id),
                ]);
            }else{
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Creates a new History model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
           if(Yii::$app->user->can('creat-history')){
                $model = new History();

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->idHistory]);
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
     * Updates an existing History model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
           if(Yii::$app->user->can('update-history')){
                $model = $this->findModel($id);

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->idHistory]);
                } else {
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
            }else{
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Deletes an existing History model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
           if(Yii::$app->user->can('delete-history')){
                $this->findModel($id)->delete();

                return $this->redirect(['index']);
            }else{
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Finds the History model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return History the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = History::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
