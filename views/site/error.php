<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $name;
?>

<div class="error-page-wrapper">
    
    <div class="card card-error border-0 shadow-sm text-center">
        <div class="card-body p-5">
            
            <div class="error-icon mb-4">
                <i class="fas fa-exclamation-circle"></i>
            </div>

            <h1 class="h3 mb-3 font-weight-bold">
                <?= Html::encode($this->title) ?>
            </h1>

            <div class="text-muted mb-4">
                <?= nl2br(Html::encode($message)) ?>
            </div>

            <p class="small text-secondary mb-4">
                Une erreur est survenue lors du traitement de votre demande.
            </p>

            <a href="<?= Url::to(['site/index']) ?>" class="btn btn-primary px-4 btn-pill">
                <i class="fas fa-home me-2"></i> Retour à l'accueil
            </a>

        </div>
    </div>

</div>