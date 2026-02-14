<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Voyage;
use app\models\Reservation;
use app\models\VoyageForm; 
use app\models\Internaute; 
use yii\web\Response;      
use yii\web\NotFoundHttpException;
use yii\helpers\Url;

class ReservationController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['creer', 'mes-reservations'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionCreer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON; 
        
        $request = Yii::$app->request;

        if ($request->isPost) {

            $voyageIdsRaw = $request->post('voyage_id');
            $nbPlaces = (int) $request->post('nb_places');

            // Split into an array
            $voyageIds = explode(',', $voyageIdsRaw);

            //check if ALL voyages are valid before booking anything
            foreach ($voyageIds as $id) {
                $voyage = Voyage::findOne($id);
                if (!$voyage) {
                    return ['message' => "Erreur : Un des trajets est introuvable.", 'messageType' => 'error'];
                }
                if (!$voyage->canItAccept($nbPlaces)) {
                     $trajet = $voyage->trajetObject;
                     return [
                        'message' => "Place insuffisante pour le trajet " . $trajet->depart . " -> " . $trajet->arrivee, 
                        'messageType' => 'error'
                     ];
                }
            }

            //Save all reservations
            $savedCount = 0;
            foreach ($voyageIds as $id) { // we do for each, in case if it is correspondence
                $reservation = new Reservation();
                $reservation->voyage = $id;
                $reservation->voyageur = Yii::$app->user->id;
                $reservation->nbplaceresa = $nbPlaces;
                if ($reservation->save()) {
                    $savedCount++;
                }
            }

            // Response
            if ($savedCount === count($voyageIds)) {
                $user = Internaute::findOne(Yii::$app->user->id);
                return [
                    'html' => $this->renderPartial('@app/views/site/profile', [
                        'user' => $user,
                        'reservations'=> $user->reservationsObject ,
                        'propositions'=> $user->voyagesConduitsObject,
                    ]),
                    'message' => "Réservation confirmée (trajet complet) !",
                    'messageType' => 'success',
                ];
            } else {
                 return ['message' => "Erreur lors de l'enregistrement d'un ou plusieurs voyage!.", 'messageType' => 'error'];
            }
        }

        // Fallback
        return [
            'html' => $this->renderPartial('@app/views/voyage/recherche-voyage', [
                'model' => new VoyageForm(),
                'voyages' => []
            ]),
        ];
    }
}