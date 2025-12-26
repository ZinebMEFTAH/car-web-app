<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class VoyageForm extends Model
{
    public $depart;
    public $arrivee;
    public $nbPersonnes;
    public $correspondance;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['depart', 'arrivee', 'nbPersonnes'], 'required'],
            [['nbPersonnes'], 'integer', 'min' => 1],
            
            ['correspondance', 'integer'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }


}