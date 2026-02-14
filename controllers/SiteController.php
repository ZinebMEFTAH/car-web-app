<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\User;
use app\models\Internaute;
use yii\helpers\Url;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        //if it is ajax return the htm of the page
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON; // important, totherwise yii expects a string
            return ['html' => $this->renderPartial('index')];
        }

        //if we arrived by link, render normally 

        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) { // if he is already logged in we point him to the home page
            return $this->actionIndex();
        }

        $model = new LoginForm();

        //handle Login Attempt (POST)
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON; 
                return [
                    // Send Redirect URL +messages
                    'redirect' => Url::to(['site/profile']), // we redirect to force page reload (navbar should be changed)
                    'message' => 'Ravi de vous revoir !',
                    'messageType' => 'success',
                ];            
            }

            return $this->redirect(['site/profile']);// fallback
        }

        // handle Display (AJAX) in case just loading the page, not vallidation login
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $response = [
                'html' => $this->renderPartial('login', ['model' => $model]),
            ];

            //only add the error message if the user actually tried to submit (POST)
            if (Yii::$app->request->isPost) {
                $response['message'] = 'Identifiants invalides';
                $response['messageType'] = 'error';
            }
            //we can add others...

            return $response;
        }

        // in case used url to reach the page 
        return $this->render('login', ['model' => $model]);
    }

    public function actionSignup()
    {
        $model = new SignupForm();//the form model 

        if ($model->load(Yii::$app->request->post()) && ($internaute = $model->signup())) {

            $identity = User::findIdentity($internaute->id);
            Yii::$app->user->login($identity); // We lgoin auto

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'redirect' => Url::to(['site/index']), // we should reload for the navbar
                    'message' => 'Bienvenue sur Covoit\'Voyages !',
                    'messageType' => 'success',
                ];            
            }

            return $this->redirect(['site/index']);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'html' => $this->renderPartial('signup', ['model' => $model]),
            ];
        }

        return $this->render('signup', ['model' => $model]);
    }
    
    public function actionLogout()
    {
        Yii::$app->user->logout();

        //  If the request comes from JavaScript (AJAX)
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'redirect' => Url::to(['site/index']),
                'message' => 'Vous avez été déconnecté.',
                'messageType' => 'success',
            ];        
        }

        // Normal fallback
        return $this->goHome();
    }

    public function actionProfile()
    {
        $identityId = Yii::$app->user->id;
        $user = Internaute::findOne($identityId);

        if (!$user) {
            return $this->redirect(['site/login']);
        }

        $params = [ // for code cleaness
            'user' => $user,
            'reservations' => $user->reservationsObject,
            'propositions' => $user->voyagesConduitsObject,
        ];

        //AJAX Request
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON; 
            return ['html' => $this->renderPartial('profile', $params)];
        }

        // Normal Request
        return $this->render('profile', $params);
    }
}