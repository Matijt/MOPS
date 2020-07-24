<?php

namespace backend\controllers;

use Yii;
use backend\models\Crop;
use backend\models\CropSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2mod\rbac\filters\AccessControl;

/**
 * CropController implements the CRUD actions for Crop model.
 */
class CropController extends Controller
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
     * Lists all Crop models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CropSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['crop'=>SORT_ASC]];
        $dataProvider->query->andWhere([' > ', 'crop.idcrops' ,2])
            ->andFilterWhere(['=', 'delete', '0'])
;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Crop model.
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
     * Creates a new Crop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Crop();

        if ($model->load(Yii::$app->request->post())) {
            $model->durationOfTheCrop = ($model->sowingFemale)+($model->transplantingFemale)+($model->pollinitionF)+($model->harvestF)+($model->harvestU);

            if($model->save()) {

                $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                if (!(strpos($actual_link, 'page') !== false)){
                    $edit = "index.php?r=crop%2Findex";
                }else{
                    $edit = 'index.php?r=crop%2Findex'."&page=".$_GET['page'];
                }
                return $this->redirect($edit);
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
     * Updates an existing Crop model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->durationOfTheCrop = ($model->sowingFemale)+($model->transplantingFemale)+($model->pollinitionF)+($model->harvestF)+($model->harvestU);
            if($model->save()) {
                $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                if (!(strpos($actual_link, 'page') !== false)){
                    $edit = "index.php?r=crop%2Findex";
                }else{
                    $edit = 'index.php?r=crop%2Findex'."&page=".$_GET['page'];
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
     * Deletes an existing Crop model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete = 1;
        $model->save();
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if (!(strpos($actual_link, 'page') !== false)){
            $edit = "index.php?r=crop%2Findex";
        }else{
            $edit = 'index.php?r=crop%2Findex'."&page=".$_GET['page'];
        }
    }

    /**
     * Finds the Crop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Crop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Crop::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
