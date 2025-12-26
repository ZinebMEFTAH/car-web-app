<?php

namespace app\models;

use yii\db\ActiveRecord;


class Reservation extends ActiveRecord
{

    /*
    int $id
    int $voyage
    int $voyageur
    int $nbplaceresa
    
    Voyage $voyage
    Internaute $voyageur
    */

    public static function tableName()
    {
        return 'fredouil.reservation';
    }

    public function rules()
    {
        return [
            [['voyage','voyageur','nbplaceresa'] ,'required'],
            [['voyage' ,'voyageur' ,'nbplaceresa'] ,'integer'],

            [['nbplaceresa'] ,'integer', 'min' => 1],
        ];
    }

    public function getVoyageObject()
    {
        return $this->hasOne(Voyage::class, [ 'id' => 'voyage' ]);
    }

    public function getVoyageurObject()
    {

        return $this->hasOne(Internaute::class, ['id' =>'voyageur']);
    }

    public function getTotalPrice(): float
    {
        if($this->voyageObject) {
            return $this->voyageObject->priceFor($this->nbplaceresa);
        } else {
            return 0;
        }
        
    }

    public static function getReservationsByVoyageId(int $voyageId)
    {

        // in case we have the id of the voyage, we can get its reservations
        return static::find()->where(['voyage' => $voyageId])->all();
    }

    // check if this reservation belongs to a voyage exact
    public function isReservationOfVoyage(Voyage $voyage): bool
    {
        return $this->voyage === $voyage->id;

    }

    public function isReservationForInternaute(Internaute $user): bool
    {
        return $this->voyageur === $user->id;
    }

    // this is so helpful, it makes us get all the reservations of an internaute
    public static function getReservationsForInternaute($userId)
        {
            // If an object was passed by mistake, we try to get the id, otherwise use the value directly
            if (is_object($userId)) {
                $userId = $userId->id;
            }

            return static::find()->where(['voyageur' => $userId])->all();
        }
}



