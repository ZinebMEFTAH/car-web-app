<?php
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Connexion';
?>
<div class="site-login container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent text-center pb-0">
                    <h3 class="text-dark">Se connecter</h3>
                    <p class="text-sm mb-0">Entrez votre pseudo et mot de passe</p>
                </div>

                <div class="card-body">
                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                        <?= $form->field($model, 'pseudo')->textInput(['placeholder' => 'Pseudo', 'autofocus' => true])->label(false) ?>

                        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Mot de passe'])->label(false) ?>

                        <?= $form->field($model, 'rememberMe')->checkbox([
                            'label' => 'Se souvenir de moi',
                        ]) ?>

                        <div class="text-center">
                            <?= Html::submitButton('Connexion', ['class' => 'btn btn-dark w-100 my-4']) ?>
                        </div>

                    <?php ActiveForm::end(); ?>
                </div>

                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                    <p class="mb-4 text-sm mx-auto">
                        Pas encore de compte ?
                        <a href="<?= Url::to(['site/signup']) ?>" class="text-primary text-gradient font-weight-bold">
                            Créer un compte
                        </a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>