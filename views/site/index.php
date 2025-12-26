<?php

/** @var yii\web\View $this */
use yii\helpers\Url;

$this->title = 'Covoit\'App - Voyagez Libre';
?>

<div class="hero-banner d-flex align-items-center justify-content-center text-center text-white">
    <div class="overlay"></div>
    <div class="container position-relative" style="z-index: 2;">
        <h1 class="display-3 fw-bold mb-3 animate-up">Voyagez moins cher,<br>voyagez ensemble.</h1>
        <p class="lead mb-5 animate-up delay-1">
            La solution de covoiturage fiable pour vos trajets quotidiens et vos grandes vacances.
        </p>
        
        <div class="d-flex justify-content-center gap-3 animate-up delay-2">
            <a href="<?= Url::to(['voyage/index']) ?>" class="btn btn-primary btn-lg btn-pill shadow-lg">
                <i class="fa fa-search me-2"></i>Trouver un trajet
            </a>
            <?php if (!Yii::$app->user->isGuest): ?>
                <a href="<?= Url::to(['voyage/create']) ?>" class="btn btn-light btn-lg btn-pill shadow-lg text-primary">
                    <i class="fa fa-plus-circle me-2"></i>Publier un trajet
                </a>
            <?php else: ?>
                <a href="<?= Url::to(['site/login']) ?>" class="btn btn-outline-light btn-lg btn-pill">
                    <i class="fa fa-user me-2"></i>Se connecter
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container section-cards">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-modern">
                <div class="icon-circle bg-blue-soft">
                    <i class="fa fa-wallet text-primary"></i>
                </div>
                <h3>Économisez</h3>
                <p class="text-muted">Divisez vos frais de route par deux ou trois. Le covoiturage est la solution la plus économique.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-modern">
                <div class="icon-circle bg-green-soft">
                    <i class="fa fa-leaf text-success"></i>
                </div>
                <h3>Écologique</h3>
                <p class="text-muted">Moins de voitures sur la route, c'est moins de CO2. Faites un geste concret pour la planète.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-modern">
                <div class="icon-circle bg-orange-soft">
                    <i class="fa fa-comments text-warning"></i>
                </div>
                <h3>Convivial</h3>
                <p class="text-muted">Ne voyagez plus seul. Rencontrez des personnes formidables et passez un trajet agréable.</p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5 mt-5 mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">C'est simple comme bonjour</h2>
        <div class="separator mx-auto"></div>
    </div>

    <div class="row align-items-center">
        <div class="col-lg-6">
            <div class="step-item d-flex align-items-center mb-4">
                <div class="step-num">1</div>
                <div>
                    <h5 class="fw-bold mb-1">Recherchez votre trajet</h5>
                    <p class="text-muted m-0">Indiquez simplement votre départ et arrivée.</p>
                </div>
            </div>
            <div class="step-item d-flex align-items-center mb-4">
                <div class="step-num">2</div>
                <div>
                    <h5 class="fw-bold mb-1">Réservez en ligne</h5>
                    <p class="text-muted m-0">Paiement sécurisé et confirmation immédiate.</p>
                </div>
            </div>
            <div class="step-item d-flex align-items-center">
                <div class="step-num">3</div>
                <div>
                    <h5 class="fw-bold mb-1">Voyagez sereinement</h5>
                    <p class="text-muted m-0">Partagez la route et les frais dans la bonne humeur.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/3063/3063822.png" alt="Carpooling" class="img-fluid floating-img" style="max-width: 80%;">
        </div>
    </div>
</div>