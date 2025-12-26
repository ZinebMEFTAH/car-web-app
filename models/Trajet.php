<?php

namespace app\models;

use yii\db\ActiveRecord;


class Trajet extends ActiveRecord
{
    /*
    int $id
    string $depart
    string $arrivee
    int $distance
    
    Voyage[] $voyages
    */
    public static function tableName()
    {
        return 'fredouil.trajet';
    }

    public function rules()
    {
        return [
            [[ 'depart','arrivee','distance' ], 'required' ],
            [[ 'depart' , 'arrivee' ],'string' , 'max'=>25],
            [['distance'], 'integer' ],
        ];
    }

    public function getVoyagesObject()
    {
        // voyage.trajet -> trajet.id
        return $this->hasMany(Voyage::class, [ 'trajet' =>'id' ]);
    }

    // models/Trajet.php
    public static function getTrajet(string $depart, string $arrivee): ?self
    {
        return static::findOne([
            'depart'  => trim($depart),
            'arrivee' => trim($arrivee),
        ]);
    }        
    public function getWritten(): string
    {
        // for exemple : "Toulouse → Marseille (412 km)" we may need it in propositions !

        return $this->depart . ' → ' . $this->arrivee . ' (' . $this->distance . ' km)' ;
    }


    public function hasVoyages(): bool
    {
        // check if this trajet has any voyages linked to it
        return $this->getVoyagesObject()->exists();
    }

    // how many voyages does this trajet has, we may need it in the search functionalities
    public function getNombreVoyages(): int
    {
        return $this->getVoyagesObject()->count();// count voyages
    }


}



