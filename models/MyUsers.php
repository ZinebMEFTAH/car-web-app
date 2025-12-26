<?php

namespace app\models;
use yii\db\ActiveRecord;

class MyUsers extends ActiveRecord
{

    public static function tableName()
    {
        // schema.table — adjust if your schema/table differ
        return 'fredouil.my_users';
    }
}
