<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Flowers;
use app\models\Internaute;
use app\models\Voyage;
use app\models\MarqueVehicule;
use app\models\Reservation;
use app\models\Trajet;
use app\models\TypeVehicule;
use app\models\VoyageForm;
use app\models\ProposeVoyageForm;

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
     * Displays search page.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new VoyageForm();
        return $this->render('recherche-voyage', [
            'model' => $model,
            'voyages' => [],
            'message' => null,
            'messageType' => null,
        ]);
    }
        
public function actionRechercherVoyage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new VoyageForm();

        // Valeurs par défaut
        $voyagesDirect = [];
        $pathsCorrespondance = []; // [INJECTED] New variable for paths
        $message = "Erreur interne lors de la recherche.";
        $messageType = 'error';
        $tableResult = "";

        try {

            if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
                $message = 'Formulaire invalide. Vérifiez les champs.';
                $messageType = 'error';
            } else {

                $departClean  = mb_convert_case($model->depart,  MB_CASE_TITLE, 'UTF-8');
                $arriveeClean = mb_convert_case($model->arrivee, MB_CASE_TITLE, 'UTF-8');
                $nbDemandes   = (int) $model->nbPersonnes;

                // -----------------------------------------------------------
                // 1. YOUR ORIGINAL DIRECT SEARCH LOGIC (PRESERVED)
                // -----------------------------------------------------------
                $trajet = Trajet::getTrajet($departClean, $arriveeClean);

                if (!$trajet) {
                    $message = "Aucun trajet n'existe entre {$departClean} et {$arriveeClean}.";
                    $messageType = 'error';
                } else {
                    $voyages = $trajet->voyagesObject;

                    if (empty($voyages)) {
                        $message = "Il n'y a actuellement aucun voyage proposé sur ce trajet.";
                        $messageType = 'warn';
                    } else {
                        // Your specific filtering logic
                        $voyagesAvecAssezDePlacesDispo = [];
                        $voyagesAvecAssezDePlaces = [];

                        foreach ($voyages as $voyage) {
                            if ($voyage->nbplacedispo >= $nbDemandes) {
                                $voyagesAvecAssezDePlaces[] = $voyage;
                                if ($voyage->canItAccept($nbDemandes)) {
                                    $voyagesAvecAssezDePlacesDispo[] = $voyage;
                                }
                            }
                        }

                        $voyagesDirect = $voyagesAvecAssezDePlaces; // Table shows all valid cars

                        if (empty($voyagesAvecAssezDePlacesDispo)) {
                            $message = "Des voyages existent sur ce trajet, mais aucun n'a assez de places pour {$nbDemandes} personne(s).";
                            $messageType = 'warn';
                        } else {
                            $nbOk = count($voyagesAvecAssezDePlacesDispo);
                            $message = "{$nbOk} voyage(s) trouvé(s) avec suffisamment de places.";
                            $messageType = 'success';
                        }
                    }
                }

                // -----------------------------------------------------------
                // 2. INJECTED: CORRESPONDENCE SEARCH
                // -----------------------------------------------------------
                if ($model->correspondance == 1) {
                    $pathsCorrespondance = Voyage::searchCorrespondences($departClean, $arriveeClean, $nbDemandes);
                }

                // -----------------------------------------------------------
                // 3. INJECTED: MERGE MESSAGES (If Correspondence Found)
                // -----------------------------------------------------------
                $nbDirectVisible = count($voyagesDirect);
                $nbCorresp       = count($pathsCorrespondance);
                $totalFound      = $nbDirectVisible + $nbCorresp;

                // If we found correspondence, we update the message to show total results
                if ($nbCorresp > 0) {
                    // Start with what direct search found
                    $directMsg = ($nbDirectVisible > 0) ? "$nbDirectVisible direct(s)" : "0 direct";
                    
                    $message = "Trouvé : $directMsg et $nbCorresp avec correspondance.";
                    $messageType = 'success';
                } 
                // If NOTHING found at all, but user asked for correspondence, update error
                elseif ($totalFound == 0 && $model->correspondance == 1) {
                    $message = "Aucun itinéraire (ni direct, ni correspondance) trouvé.";
                    $messageType = 'warn';
                }
                // Else: We keep your original specific message from Section 1
            }

            // -----------------------------------------------------------
            // 4. INJECTED: SAFE RENDER (Inside Try Block)
            // -----------------------------------------------------------
            $tableResult = $this->renderPartial('table-result', [
                'model'      => $model,
                'voyages'    => $voyagesDirect,
                'paths'      => $pathsCorrespondance, // Pass paths
                'messageNew' => false,
            ]);

        } catch (\Throwable $e) {
            // Catch View Errors or Logic Errors
            Yii::error($e->getMessage(), __METHOD__);
            return [
                'tableResult' => '<div class="alert alert-danger">
                                    <strong>Erreur :</strong> ' . $e->getMessage() . 
                                    '<br><small>Fichier: ' . $e->getFile() . ' (Ligne ' . $e->getLine() . ')</small>
                                  </div>',
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
        // Security Check: Drivers Only
        $identity = Yii::$app->user->identity;
        if (!$identity || empty($identity->permis)) {
            Yii::$app->session->setFlash('error', "Vous devez avoir un permis pour proposer un voyage.");
            return $this->goHome();
        }

        $model = new ProposeVoyageForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Votre voyage a été publié avec succès !");
            return $this->redirect(['site/profile']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}