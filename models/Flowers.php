<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class Flowers extends Model
{
    public $produits;

    public function __construct(){
        $this->produits =[
            '1' => [
                'id' => '1',
                'produit' => 'Rose',
            ],
            '2' => [
                'id' => '2',
                'produit' => 'Tulipe',
            ],
            '3' => [
                'id' => '3',
                'produit' => 'Jasmin',
            ],
            '4' => [
                'id' => '4',
                'produit' => 'Laurier Rose',
            ],
            '5' => [
                'id' => '5',
                'produit' => 'Orchidée',
            ],
        ];
    }



}
