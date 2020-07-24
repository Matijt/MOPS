<?php
namespace backend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use backend\models\SignupForm;
use frontend\models\ContactForm;
use backend\models\AuthItem;

/**
 * Site controller
 */
class SiteController extends Controller
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
                        'actions' => ['logout', 'index', 'signup'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (array_key_exists('Viewer', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Holland', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
            return $this->redirect(['order/index']);
        }elseif (array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))){
            return $this->redirect(['estimations/index']);
        }elseif (array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))){
            return $this->redirect(['order/index']);
        }elseif (array_key_exists('Estimator Helper', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))){
            return $this->redirect(['registry/index']);
        }else{
            return $this->render('index');
        }
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = 'login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            try{
                if (array_key_exists('Viewer', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Holland', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
                    return $this->redirect(['order/index']);
                }else if (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))){
                    return $this->goBack();
                }else if (array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))){
                    return $this->redirect(['estimations/index']);
                }elseif (array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))){
                    return $this->redirect(['order/index']);
                 }else if (array_key_exists('Estimator Helper', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))){
                    return $this->redirect(['registry/index']);
                }else{
                    Yii::$app->session->setFlash('danger', "You do not have permissions, contact with the administrator.");
                    $this->actionLogout();
                }
            }catch(Exception $e){
                return $this->goBack();
            }
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    public function actionSignup()
    {
        $model = new SignupForm();
        $authItem = AuthItem::find()->all();
        if ($model->load(Yii::$app->request->post())) {
            print_r($model->getErrors());
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
            'authItem' => $authItem,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}

