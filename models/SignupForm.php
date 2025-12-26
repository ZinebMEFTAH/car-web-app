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
    
    public $permis;
    public $hasPermis; // <--- NEW: To remember the checkbox state

    public function rules() {
        return [
            [['pseudo', 'pass', 'nom', 'prenom', 'mail'], 'required'],
            ['mail', 'email'],
            ['pseudo', 'unique', 'targetClass' => Internaute::class, 'message' => 'Ce pseudo est déjà pris.'],
            ['pass', 'string', 'min' => 4],
            ['photo', 'url'],
            
            // Boolean rule for the checkbox
            ['hasPermis', 'boolean'],

            // CONDITIONAL RULE: Permis is REQUIRED only if hasPermis is Checked (1)
            ['permis', 'required', 'when' => function($model) {
                return $model->hasPermis == 1;
            }, 'enableClientValidation' => false, 'message' => 'Veuillez renseigner le numéro de permis.'],
            
            [['permis'], 'string', 'max' => 50],
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

        // LOGIC: Only save permis if the box was checked. Otherwise null.
        if ($this->hasPermis) {
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