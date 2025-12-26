<?php

namespace app\models;

use yii\db\ActiveRecord;


class MarqueVehicule extends ActiveRecord
{
    /*
    int $id
    string $marquev
    
    Voyage[] $voyages
    */

    public static function tableName()
    {
        return 'fredouil.marquevehicule';
    }

    public function rules()
    {
        return [
            [['marquev'], 'required'],
            [['marquev'], 'string', 'max'=> 25],
        ];
    }
}