<?php
namespace app\models;

use yii\base\Model;
use app\models\Internaute;

class SignupForm extends Model {
    
    public $pseudo;
    public $pass;
    public $nom;
    public $prenom;
    public $mail;
    public $photo;
    public $permis; // it is optional, if the user enters it menas he is a driver possible

    public function rules() {
        return [
            [[ 'pseudo' , 'pass', 'nom', 'prenom', 'mail'], 'required'],
            [ 'mail' , 'email' ] ,
            [ 'pseudo' , 'unique', 'targetClass'=>Internaute::class, 'message' => 'Ce pseudo est déjà pris.'],
            [ 'pass' , 'string', 'min'  => 4],
            ['photo', 'url'] ,
            ['permis', 'integer', 'message' =>'Le numéro de permis doit être un nombre valide.' ],
        ];
    }

    public function signup() {
        if (!$this->validate()) return null;

        $internaute = new Internaute();
        $internaute->pseudo = $this->pseudo;
        $internaute->nom = $this->nom;
        $internaute->prenom = $this->prenom;
        $internaute->mail = $this->mail;
        $internaute->pass = sha1($this->pass);

        //Save permis only if filled
        if (!empty($this->permis)) {
            $internaute->permis = $this->permis;
        } else {

            $internaute->permis = null;
        }

        if (empty($this->photo)) {
            $internaute->photo = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
            
        } else {
            $internaute->photo = $this->photo;
        }

        return $internaute->save() ? $internaute : null;
    }
}