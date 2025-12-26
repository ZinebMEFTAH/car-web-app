<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Voyage;
use app\models\Reservation;
use yii\web\NotFoundHttpException;

class ReservationController extends Controller
{
    // Ensure only logged-in users can reserve
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['creer', 'mes-reservations'],
                        'roles' => ['@'], // '@' = Logged In users only
                    ],
                ],
            ],
        ];
    }

    public function actionCreer()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            $voyageId = $request->post('voyage_id');
            $nbPlaces = (int) $request->post('nb_places');

            $voyage = Voyage::findOne($voyageId);

            if (!$voyage) {
                throw new NotFoundHttpException("Voyage introuvable.");
            }

            // 1. Double check capacity (Security)
            if (!$voyage->canItAccept($nbPlaces)) {
                Yii::$app->session->setFlash('error', "Désolé, ce voyage n'a plus assez de places.");
                return $this->redirect(['voyage/index']);
            }

            // 2. Create Reservation
            $reservation = new Reservation();
            $reservation->voyage = $voyage->id;
            $reservation->voyageur = Yii::$app->user->id; // Current User
            $reservation->nbplaceresa = $nbPlaces;

            if ($reservation->save()) {
                Yii::$app->session->setFlash('success', "Réservation confirmée avec succès !");
                // 3. Redirect to Profile / My Reservations
                return $this->redirect(['site/profile']);
            } else {
                Yii::$app->session->setFlash('error', "Erreur lors de la réservation.");
            }
        }

        return $this->redirect(['voyage/index']);
    }

    public function actionMesReservations()
    {
        // Get current user ID directly
        $userId = Yii::$app->user->id; 
        
        // Pass the ID, not the object
        $reservations = Reservation::getReservationsForInternaute($userId);

        return $this->render('mes-reservations', [
            'reservations' => $reservations
        ]);
    }
}