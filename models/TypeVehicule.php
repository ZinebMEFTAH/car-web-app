<?php

namespace app\models;

use yii\db\ActiveRecord;


class TypeVehicule extends ActiveRecord
{
    /*
    int $id
    string $typev
    
    Voyage[] $voyages
    */

    public static function tableName()
    {
        return 'fredouil.typevehicule';
    }

    public function rules()
    {
        return [           
             [['typev' ],'string' , 'max'=>25],

            [['typev'], 'required' ],
        ];
    }

}
