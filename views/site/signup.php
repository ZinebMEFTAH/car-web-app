<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Inscription';
?>
<div class="site-signup container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent text-center pb-0">
                    <h3 class="text-dark">Créer un compte</h3>
                    <p class="text-sm mb-0">Rejoignez la communauté Covoit'Voyages</p>
                </div>

                <div class="card-body">
                    <?php 
                    // start form, data sent to actionSignup
                    $form = ActiveForm::begin([
                                            'id' => 'form-signup',
                                            'action' => ['site/signup'],
                                            'method' => 'post',
                                        ]); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'pseudo')->textInput(['autofocus' => true, 'placeholder' => 'Pseudo'])->label('Pseudo') ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'mail')->input('email', ['placeholder' => 'exemple@email.com'])->label('Email') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'prenom')->textInput(['placeholder' => 'Prénom']) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'nom')->textInput(['placeholder' => 'Nom']) ?>
                            </div>
                        </div>

                        <?= $form->field($model, 'pass')->passwordInput(['placeholder' => 'Mot de passe'])->label('Mot de passe') ?>

                        <?= $form->field($model, 'photo')->textInput([
                            'placeholder' => 'https://example.com/photo.jpg'
                        ])->label('Photo de profil (URL) - Optionnel') ?>
                        
                        <div class="my-3 p-3 bg-light rounded border">
                            <h6 class="text-muted text-uppercase font-weight-bold mb-3" style="font-size: 0.75rem;">Conducteur (Optionnel)</h6>
                            
                            <?= $form->field($model, 'permis')->textInput([
                                'placeholder' => 'Numéro du permis (Ex: 123456789)',
                            ])->label('Numéro de permis') ?>
                            
                            <small class="text-muted text-xs">
                                Remplissez ce champ uniquement si vous souhaitez proposer des voyages.
                            </small>
                        </div>

                        <div class="text-center">
                            <?= Html::submitButton("S'inscrire", ['class' => 'btn btn-dark w-100 my-4']) ?>
                        </div>

                    <?php ActiveForm::end(); ?>
                </div>

                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                    <p class="mb-4 text-sm mx-auto">
                        Vous avez déjà un compte ? 
                        <a href="<?= Url::to(['site/login']) ?>" class="text-primary text-gradient font-weight-bold">
                            Se connecter
                        </a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>