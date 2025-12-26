<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = 'Ma Page';
?>
<div class="site-mapage">
    <h1><?= Html::encode($message) ?></h1>

    <p>
        This is my Personal page!
    </p>

    <?php

    $Fruits = ArrayHelper::map($model->produits, 'id', 'produit');

    echo Html::dropDownList(
        'Fruit',
        null, //the default value when we enter we find it can be 1 ...
        $Fruits,
        ['prompt' => 'Please select your favorite Fruite!']
    )


    ?>

</div>
