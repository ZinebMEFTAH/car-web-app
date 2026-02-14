<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\VoyageForm;
use app\models\ProposeVoyageForm;
use app\models\Trajet;
use app\models\Voyage;
use app\models\MarqueVehicule;
use app\models\TypeVehicule;
use yii\helpers\Url;
use app\models\Internaute;
use yii\helpers\ArrayHelper;

class VoyageController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
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

    /**
     * Displays search page.
     */
    public function actionIndex()
    {
        $model = new VoyageForm();
        
        //AJAX, return JSON
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON; 
            return [
                'html' => $this->renderPartial('recherche-voyage', [
                    'model' => $model,
                    'voyages' => [],
                ]),
            ];
        }

        // Direct Access (Reload), return Full Page
        return $this->render('recherche-voyage', [
            'model' => $model,
            'voyages' => [],
        ]);
    }
        
    public function actionRechercherVoyage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new VoyageForm();
        
        $voyagesDirect = [];
        $pathsCorrespondance = [];
        
        $countDirectDisplayed = 0;
        $countDirectAvailable = 0;
        
        $message = "Erreur interne.";
        $messageType = 'error';

        try {
            if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
                $message = 'Formulaire invalide. Vérifiez les champs.';
                $messageType = 'error';
            } else {
                $departClean  = mb_convert_case($model->depart,  MB_CASE_TITLE, 'UTF-8');
                $arriveeClean = mb_convert_case($model->arrivee, MB_CASE_TITLE, 'UTF-8');
                $nbDemandes   = (int) $model->nbPersonnes;

                // **** DIRECT VOYAGES

                $trajet = Trajet::getTrajet($departClean, $arriveeClean);

                if ($trajet) {
                    foreach ($trajet->voyagesObject as $voyage) {
                        
                        // if the car is too small for the request (original capacity), we don't even show it
                        if ($voyage->nbplacedispo >= $nbDemandes) {
                            
                            $voyagesDirect[] = $voyage; 
                            $countDirectDisplayed++;

                            // check if there are actually enough seats left right now
                            if ($voyage->canItAccept($nbDemandes)) {
                                $countDirectAvailable++;
                            }
                        }
                    }
                }

                // **** CORRESPONDENCE VOYAGES

                if ($model->correspondance == 1) {
                    $rawPaths = Voyage::searchCorrespondences($departClean, $arriveeClean, $nbDemandes);
                    
                    foreach ($rawPaths as $path) {
                        if (count($path) < 2) continue;
                        if (!$path[0]->trajetObject || !$path[count($path)-1]->trajetObject) continue;

                        // verify that every part of the correspondence has enough space
                        $allLegsOk = true;
                        foreach ($path as $leg) {
                            if (!$leg->canItAccept($nbDemandes)) {
                                $allLegsOk = false;
                                break; 
                            }
                        }

                        if ($allLegsOk) {
                            $pathsCorrespondance[] = $path;
                        }
                    }
                }

                $countCorresp = count($pathsCorrespondance);

                //****MESSAGES
                
                // nothing found
                if ($countDirectDisplayed == 0 && $countCorresp == 0) {
                    $message = "Aucun voyage trouvé pour ces critères.";
                    $messageType = 'warn'; 
                    
                    if (!$trajet && $countCorresp == 0) {
                         $message = "Aucun trajet n'existe entre {$departClean} et {$arriveeClean}.";
                         $messageType = 'error';
                    }
                }
                // voyages exist but are full
                elseif ($countDirectAvailable == 0 && $countCorresp == 0) {
                    $message = "Des voyages existent, mais places insuffisantes.";
                    $messageType = 'warn';
                }
                // success
                else {
                    $messageType = 'success';
                    
                    if ($model->correspondance == 1) {
                        $parts = [];
                        if ($countDirectDisplayed > 0) {
                            $parts[] = "$countDirectDisplayed direct(s)";
                        }
                        if ($countCorresp > 0) {
                            $parts[] = "$countCorresp avec correspondance";
                        }
                        $message = "Trouvé : " . implode(" et ", $parts) . ".";
                    } else {
                        $message = "$countDirectDisplayed voyage(s) trouvé(s).";
                    }
                }
            }

            $tableResult = $this->renderPartial('table-result', [
                'model'      => $model,
                'voyages'    => $voyagesDirect,
                'paths'      => $pathsCorrespondance,
                'messageNew' => false,
            ]);

        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return [
                'tableResult' => '<div class="alert alert-danger">Erreur: ' . $e->getMessage() . '</div>',
                'message' => 'Erreur critique détectée.',
                'messageType' => 'error'
            ];
        }

        return [
            'tableResult' => $tableResult,
            'message'     => $message,
            'messageType' => $messageType,
        ];
    }

    public function actionCreate()
    {
        $identity = Yii::$app->user->identity;
        
        //verify if he has a license, otherwise he cannot propose a trip
        if (!$identity || empty($identity->permis)) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'html' => $this->renderPartial('recherche-voyage', ['model' => new VoyageForm()]),
                    'message' => "Vous devez avoir un permis.",
                    'messageType' => 'error',
                ];
            }
            return $this->redirect(['voyage/index']);
        }

        $model = new ProposeVoyageForm();

        // if the form is sent and saved correctly
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                // we reload the user to get the fresh data (including the new trip)
                $user = Internaute::findOne(Yii::$app->user->id);

                return [
                    'html' => $this->renderPartial('@app/views/site/profile', [
                        'user' => $user,
                        'reservations' => $user->reservationsObject,
                        'propositions' => $user->voyagesConduitsObject,
                    ]),
                    'message' => 'Voyage publié avec succès',
                    'messageType' => 'success',
                ];
            }
            // simple fallback for non-ajax
            return $this->redirect(['site/profile']);
        }

        // get the lists for the dropdowns
        $types = ArrayHelper::map(TypeVehicule::find()->asArray()->all(), 'id', 'typev');
        $marques = ArrayHelper::map(MarqueVehicule::find()->asArray()->all(), 'id', 'marquev');

        // show the form (first time or if there are errors)
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $response = [
                'html' => $this->renderPartial('create', [
                    'model' => $model,
                    'types' => $types,
                    'marques' => $marques
                ]),
            ];

            // if there are validation errors after the submit, we notify the user
            if ($model->hasErrors()) {
                $response['message'] = "Veuillez corriger les erreurs dans le formulaire.";
                $response['messageType'] = 'error';
            }

            return $response;
        }

        return $this->render('create', ['model' => $model, 'types' => $types, 'marques' => $marques]);
    }
}