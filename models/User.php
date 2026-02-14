<?php

namespace app\models;

use yii\base\BaseObject;
use yii\web\IdentityInterface;

class User extends BaseObject implements IdentityInterface
{
    public $id;
    public $pseudo;
    public $pass;
    public $prenom; // navbar uses it
    public $permis;

    /**
     * Restore user from session
     */
    public static function findIdentity($id)
    {
        $internaute = Internaute::findOne($id);
        
        return $internaute ? new static([
            'id' => $internaute->id, 
            'pseudo' => $internaute->pseudo, 
            'pass' => $internaute->pass,
            'prenom' => $internaute->prenom,
            'permis' => $internaute->permis, 
        ]) : null;
    }    

    /**
     * Find user for login
     */
    public static function findByUsername($pseudo)
    {
        $internaute = Internaute::findOne(['pseudo' => $pseudo]);
        
        return $internaute ? new static([
            'id' => $internaute->id, 
            'pseudo' => $internaute->pseudo, 
            'pass' => $internaute->pass,
            'prenom' => $internaute->prenom,
            'permis' => $internaute->permis, 
        ]) : null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function validatePassword($password)
    {
        return sha1($password) === $this->pass;
    }

    public static function findIdentityByAccessToken($token, $type = null) { return null; }
    public function getAuthKey() { return null; }
    public function validateAuthKey($authKey) { return false; }
}