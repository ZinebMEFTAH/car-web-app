<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

// Models
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm; // <--- Added this
use app\models\User;       // <--- Added this (for auto-login after signup)
use app\models\Internaute;
use app\models\Flowers;

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
                'only' => ['logout', 'signup'], // Restrict logout and signup
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'], // Only logged-in users can logout
                    ],
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'], // Only guests can signup
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Signup action.
     * Handles new user registration.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($internaute = $model->signup()) {
                // Registration successful. 
                // Now find the Identity (User) from the Database Object (Internaute)
                $identity = User::findIdentity($internaute->id);
                
                // Log the user in automatically
                if (Yii::$app->user->login($identity)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionMaPage()
    {
        $msg = Yii::$app->request->get('msg', 'Hello World');
        $msg = mb_substr(strip_tags($msg), 0, 200);
        $model = new Flowers();

        return $this->render('MaPage', ['message' => $msg, 'model' => $model]);
    }

    public function actionTestInternaute($pseudo = 'Fourmi')
    {
        // 1. Charger l’internaute par pseudo
        $user = Internaute::getUserByIdentifiant($pseudo);

        if ($user === null) {
            throw new \yii\web\NotFoundHttpException("Internaute '$pseudo' introuvable");
        }

        // 2. Passer l’objet à la vue
        return $this->render('test-internaute', [
            'user' => $user,
        ]);
    }

    public function actionProfile()
    {
        // 1. Get the real database object of the current user
        $identityId = Yii::$app->user->id;
        $user = Internaute::findOne($identityId);

        if (!$user) {
            return $this->redirect(['site/login']);
        }

        // 2. Get Reservations (Trips I am a PASSENGER)
        // We use the relation defined in Internaute.php
        $reservations = $user->reservationsObject;

        // 3. Get Propositions (Trips I am a DRIVER)
        // Only meaningful if user has a permit, but we can query it anyway
        $propositions = $user->voyagesConduitsObject;

        return $this->render('profile', [
            'user' => $user,
            'reservations' => $reservations,
            'propositions' => $propositions,
        ]);
    }

}