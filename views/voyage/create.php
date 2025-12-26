<?php

/** @var yii\web\View $this */
/** @var app\models\ProposeVoyageForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use app\models\TypeVehicule;
use app\models\MarqueVehicule;

$this->title = 'Proposer un voyage';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-transparent text-center pb-0">
                    <h3 class="text-dark">Proposer un nouveau trajet</h3>
                    <p class="text-sm mb-0">Remplissez les détails de votre voyage</p>
                </div>
                
                <div class="card-body">
                    
                    <?php $form = ActiveForm::begin(['id' => 'form-propose-voyage']); ?>

                    <h6 class="text-muted text-uppercase font-weight-bold mb-3 mt-2">Trajet</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'depart')->textInput(['placeholder' => 'Départ (ex: Paris)']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'arrivee')->textInput(['placeholder' => 'Arrivée (ex: Avignon)']) ?>
                        </div>
                    </div>

                    <h6 class="text-muted text-uppercase font-weight-bold mb-3 mt-4">Détails</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'heuredepart')->input('number', ['min' => 0, 'max' => 23, 'placeholder' => 'Ex: 14'])->label('Heure (0-23h)') ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'nbplacedispo')->input('number', ['min' => 1, 'placeholder' => 'Ex: 3'])->label('Places dispo') ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'prix_total')->input('number', ['min' => 0, 'step' => '0.5', 'placeholder' => 'Ex: 25'])->label('Prix /pers (€)') ?>
                        </div>
                    </div>

                    <h6 class="text-muted text-uppercase font-weight-bold mb-3 mt-4">Véhicule & Options</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'nbbagage')->dropdownList([
                                0 => 'Aucun bagage',
                                1 => '1 Bagage moyen',
                                2 => '2 Bagages moyens',
                                3 => 'Gros bagages'
                            ])->label('Bagages') ?>
                        </div>

                        <div class="col-md-4">
                            <?php 
                                // Maps 'id' to 'typev' from database
                                $types = ArrayHelper::map(TypeVehicule::find()->all(), 'id', 'typev'); 
                            ?>
                            <?= $form->field($model, 'idtypev')->dropdownList($types, [
                                'prompt' => 'Sélectionnez un type...'
                            ])->label('Type') ?>
                        </div>

                        <div class="col-md-4">
                             <?php 
                                // Maps 'id' to 'marquev' from database
                                $marques = ArrayHelper::map(MarqueVehicule::find()->all(), 'id', 'marquev'); 
                             ?>
                             <?= $form->field($model, 'idmarquev')->dropdownList($marques, [
                                 'prompt' => 'Sélectionnez une marque...'
                             ])->label('Marque') ?>
                        </div>
                    </div>

                    <div class="mt-2">
                        <?= $form->field($model, 'contraintes')->textarea(['rows' => 3, 'placeholder' => 'Ex: Animaux interdits, non fumeur, musique...']) ?>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <?= Html::submitButton('Publier le voyage', ['class' => 'btn btn-dark btn-lg w-100']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>