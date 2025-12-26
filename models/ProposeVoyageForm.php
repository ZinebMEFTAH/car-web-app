<?php

namespace app\models;

use yii\base\Model;
use Yii;
use app\models\Trajet;
use app\models\Voyage;

class ProposeVoyageForm extends Model
{
    public $depart;
    public $arrivee;
    // public $distance; // REMOVED: You cannot define distance for read-only routes
    
    public $heuredepart;
    public $nbplacedispo;
    public $nbbagage;
    public $prix_total;
    
    public $idtypev;
    public $idmarquev;
    public $contraintes;

    public function rules()
    {
        return [
            // Removed 'distance' from required
            [['depart', 'arrivee', 'heuredepart', 'nbplacedispo', 'prix_total', 'nbbagage', 'idtypev', 'idmarquev'], 'required'],
            [['depart', 'arrivee'], 'string', 'max' => 25],
            [['nbplacedispo', 'nbbagage', 'idtypev', 'idmarquev'], 'integer', 'min' => 1],
            ['heuredepart', 'integer', 'min' => 0, 'max' => 23],
            ['prix_total', 'number', 'min' => 0],
            [['contraintes'], 'string', 'max' => 500],
        ];
    }

    public function save()
    {
        if (!$this->validate()) return false;

        $villeDepart = mb_convert_case(trim($this->depart), MB_CASE_TITLE, "UTF-8");
        $villeArrivee = mb_convert_case(trim($this->arrivee), MB_CASE_TITLE, "UTF-8");

        // 1. Find Existing Trajet
        $trajet = Trajet::find()
            ->where(['depart' => $villeDepart, 'arrivee' => $villeArrivee])
            ->one();

        // IF NOT FOUND: We show an error instead of crashing the DB
        if (!$trajet) {
            $this->addError('depart', "Ce trajet ({$villeDepart} → {$villeArrivee}) n'existe pas dans la base de référence.");
            return false;
        }

        // 2. Create Voyage
        $voyage = new Voyage();
        $voyage->conducteur = Yii::$app->user->id;
        $voyage->trajet = $trajet->id; // Use the existing ID
        
        $voyage->heuredepart = $this->heuredepart;
        $voyage->nbplacedispo = $this->nbplacedispo;
        $voyage->nbbagage = $this->nbbagage;
        $voyage->idtypev = $this->idtypev;
        $voyage->idmarquev = $this->idmarquev;
        $voyage->contraintes = $this->contraintes;

        // Calculate Tarif based on the Database Distance
        if ($trajet->distance > 0) {
            $voyage->tarif = $this->prix_total / $trajet->distance;
        } else {
            $voyage->tarif = 0;
        }

        return $voyage->save();
    }
}