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
    // public $distance; // it is read only data so no need to get the distance from user ! 
    
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
            [['depart','arrivee', 'heuredepart', 'nbplacedispo', 'prix_total' , 'nbbagage', 'idtypev', 'idmarquev'], 'required'],
            [['depart', 'arrivee'] , 'string', 'max'=> 25],
            [['nbplacedispo' ,'nbbagage' , 'idmarquev'], 'integer', 'min' => 1],
            ['heuredepart',  'integer', 'min'=> 0 ,'max' => 23],
            ['prix_total' , 'number', 'min' =>0],
            [['contraintes'] , 'string', 'max'=>500],
            [ ['nbbagage'], 'string', 'max'=>200 ] ,

        ];
    }

    public function save()
    {
        if (!$this->validate()) return false;

        $villeDepart = mb_convert_case(trim($this->depart), MB_CASE_TITLE, "UTF-8");
        $villeArrivee = mb_convert_case(trim($this->arrivee), MB_CASE_TITLE, "UTF-8");

        // we get the trajet
        $trajet = Trajet::findOne(['depart' => $villeDepart, 'arrivee' => $villeArrivee]);

        // we check it does really exist!
        if (!$trajet) {

            $this->addError('depart', "Ce trajet ({$villeDepart} → {$villeArrivee}) n'existe pas dans la base de référence.");
            return false;
        }

        // we create the  voyage
        $voyage = new Voyage();
        $voyage->conducteur = Yii::$app->user->id;
        $voyage->trajet = $trajet->id; 
        $voyage->heuredepart = $this->heuredepart;
        $voyage->nbplacedispo = $this->nbplacedispo;
        $voyage->nbbagage = $this->nbbagage;
        $voyage->idtypev = $this->idtypev;
        $voyage->idmarquev = $this->idmarquev;
        $voyage->contraintes = $this->contraintes;

        // Calculate price based on the Database Distance
        if ($trajet->distance > 0) {
            $voyage->tarif = $this->prix_total / $trajet->distance;
        } else {
            $voyage->tarif = 0;
        }

        return $voyage->save();
    }
}