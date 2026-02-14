<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $pseudo; 
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function rules()
    {
        return [
            [['pseudo', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            
            $user = $this->getUser();

        if ( !$user ||!$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect pseudo or password.');
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            // Log in the user for 30 days if rememberMe is true
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->pseudo);
        }
        return $this->_user;
    }
}