<?php

namespace backend\controllers;
require_once '../../vendor/autoload.php';

use backend\models\Cita;
use backend\models\Model;
use backend\models\Restriccion;
use backend\models\SignupForm;
use common\models\User;
use backend\models\ChangePasswordForm;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\UserSearch;
use yii2mod\rbac\migrations\Migration;
use backend\models\AuthAssignment;


/**
 * UserHasRestriccionController implements the CRUD actions for UserHasRestriccion model.
 */
class LoginController extends Controller
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
     * Lists all UserHasRestriccion models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (array_key_exists('Doctor', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
            $assigments = AuthAssignment::find()->andFilterWhere(['!=', 'item_name', 'Cliente'])->all();
            foreach ($assigments AS $assigment){
                $dataProvider->query->andFilterWhere(['!=', 'id', $assigment->user_id]);
            }
        }else if (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))){
            echo "<script>console.log('Administrator')</script>";
        }else{
            echo "You have no access";
            die;
        }
        $dataProvider->query->orderBy('username');
        return $this->render('suindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserHasRestriccion model.
     * @param integer $user_id
     * @param integer $restriccion_idrestriccion
     * @param integer $restriccion_estado_idestado
     * @param integer $restriccion_dieta_idnombre_dieta
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($user_id)
    {

        $updatep = new ChangePasswordForm($user_id);
        return $this->renderAjax('view', [
            'model' => $this->findModel($user_id),
        ]);
    }

    /**
     * Creates a new UserHasRestriccion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->signup()) {
                $mig = new Migration();
                $user = User::find()->andFilterWhere(['=','username',$model->username])->one();
                $mig->authManager->assign($mig->authManager->getRole('Viewer'), $user->attributes['id']);
                Yii::$app->session->setFlash('success', "User: ".$model->username." created.");
                        echo "<script>window.history.back();</script>";
                        die;
            }
            else{
                print_r($model->getErrors()['username'][0]);
                $properties = [
                    'disabled' => true,
                ];
                Yii::$app->session->setFlash('danger', "User not created.");
                echo "<script>window.history.back();</script>";
                die;
            }
        }


        return $this->renderAjax('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserHasRestriccion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($user_id)
    {
        $model = $this->findModel($user_id);
        $updatep = new ChangePasswordForm($user_id);
        $auth_key = $model->auth_key;
        $rol = \Yii::$app->authManager->getRolesByUser($model->id);
        $rol = (end($rol)->name);
        $model->auth_key = $rol;

        if ($model->load(Yii::$app->request->post()) && $updatep->load(Yii::$app->request->post())) {
            $assigment = AuthAssignment::find()->andFilterWhere(['=', 'user_id', $user_id])->one();
            $assigment->item_name = ($_POST["User"]["auth_key"]);
            $assigment->save();
            $model->auth_key = $auth_key;
            if($model->save() && $updatep->changePassword()){
                Yii::$app->session->setFlash('warning', "User: ".$model->username." Updated correctly.");
            }else{
                Yii::$app->session->setFlash('danger', "User NOT updated.");
            }
            echo "<script>window.history.back();</script>";
            die;
        }

        return $this->renderAjax('update', [
            'model' => $model,
            'updatep' => $updatep,
        ]);
    }
    public function actionUpdater($user_id)
    {
        $model = $this->findModel($user_id);
        $updatep = new ChangePasswordForm($user_id);
        $auth_key = $model->auth_key;
        $rol = \Yii::$app->authManager->getRolesByUser($model->id);
        $rol = (end($rol)->name);
        $model->auth_key = $rol;

        if ($model->load(Yii::$app->request->post())){
            $assigment = AuthAssignment::find()->andFilterWhere(['=', 'user_id', $user_id])->one();
            $assigment->item_name = ($_POST["User"]["auth_key"]);
            if ($assigment->save()){
                echo "yes";
            }else{
                echo "no";
            }
            echo "<script>window.history.back();</script>";
            die;
        }

        return $this->renderAjax('updater', [
            'model' => $model,
            'updatep' => $updatep,
        ]);
    }

    /**
     * Deletes an existing UserHasRestriccion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $user_id
     * @param integer $restriccion_idrestriccion
     * @param integer $restriccion_estado_idestado
     * @param integer $restriccion_dieta_idnombre_dieta
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($user_id, $restriccion_idrestriccion, $restriccion_estado_idestado, $restriccion_dieta_idnombre_dieta)
    {
        $this->findModel($user_id, $restriccion_idrestriccion, $restriccion_estado_idestado, $restriccion_dieta_idnombre_dieta)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserHasRestriccion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $user_id
     * @param integer $estado_idestado
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($user_id)
    {
        if (($model = User::findOne(['id' => $user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    public function actionUser()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('suindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUsercreate()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->signup()) {
                $user = User::find()->andFilterWhere(['=','email',$model->email])->one();
                print_r($user->attributes['id']);
                die;
                return $this->redirect(['/rbac']);
            }
        }

        $modelcita = new Cita();
        $modelrestriccion = new Restriccion();
        $modelhasrestriccion = new RestriccionesDeUsuario();

        return $this->renderAjax('signup', [
            'model' => $model,
            'modelcita' => $modelcita,
            'modelrestriccion' => $modelrestriccion,
            'modelhasrestriccion' => $modelhasrestriccion,
        ]);
    }

    public function actionSetDieta($res){
        echo $res;
    }


    public function actionSetdate()
    {
        echo "kasmklsmdkmlsamklamd";
    }
    /**
     * Updates an existing UserHasRestriccion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $user_id
     * @param integer $restriccion_idrestriccion
     * @param integer $restriccion_estado_idestado
     * @param integer $restriccion_dieta_idnombre_dieta
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionProfile()
    {
        $model = $this->findModel(Yii::$app->user->id);
        $updatep = new ChangePasswordForm(Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()) && $updatep->load(Yii::$app->request->post())) {
            $model->email = $updatep->email;
            $model->save();
            $updatep->changePassword();
            echo "<script>window.history.back();</script>";
            die;
        }
        $updatep->email = $model->email;

        return $this->render('profile', [
            'model' => $model,
            'updatep' => $updatep,
        ]);
    }
}
