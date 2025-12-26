<?php

namespace app\models;

use yii\db\ActiveRecord;


class Internaute extends ActiveRecord
{
    /*
    int $id
    string $pseudo
    string $pass
    string $nom
    string $prenom
    string $mail
    string $photo
    string|null $permis
    
    Voyage[] $voyagesConduits
    Reservation[] $reservations
    */

    public static function tableName()
    {
        return 'fredouil.internaute';
    }

    public function rules()
    {
        return [

            [['pseudo', 'pass', 'nom', 'prenom', 'mail'], 'required'],

            ['photo', 'string', 'max' => 200],
            [['pseudo', 'pass', 'nom', 'prenom', 'mail'], 'string', 'max' => 45],

            ['permis', 'number'],
            ['mail', 'email'],
            ['pseudo', 'unique'],
        ];
    }

    public function getVoyagesConduitsObject()
    {
        // the table voyage has a column conducteur (the id) that is why we map like this, however here due to YII it is an object !
        // voyage.conducteur -> internaute.id
        return $this->hasMany(Voyage::class, ['conducteur' => 'id']);
    }

    public function getReservationsObject()
    {
        // same idea that an internaute can have many reservations (since he can be a traveler as well)
        // reservation.voyageur -> internaute.id
        return $this->hasMany(Reservation::class, ['voyageur' => 'id']);
    }

    public function isConducteur(): bool
    {
        // in the form of registeration if the user enters his driver licence number, we take him as a driver possible auto
        return !empty($this->permis);
    }

    public static function getUserByIdentifiant(string $pseudo)
    {
        // a static function helps in code when we need an internaute object and we have only his pseaudo
        return static::findOne(['pseudo' => $pseudo]);
    }

    public function getNombreReservations(): int
    {
        return $this->getReservationsObject()->count();
    }

    public function getNombreVoyagesConduits(): int
    {
        if (!$this->isConducteur()){
            return 0;
        }
        return $this->getVoyagesConduitsObject()->count();
    }

    public function getFullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function voyageInternauteReservation(Voyage $voyage): bool
    {
        return Reservation::find()->where(['voyageur' => $this->id, 'voyage' => $voyage->id])->exists();
    }
}