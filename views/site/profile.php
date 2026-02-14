<?php
/** @var yii\web\View $this */
/** @var app\models\Internaute $user */
/** @var app\models\Reservation[] $reservations */
/** @var app\models\Voyage[] $propositions */

use yii\helpers\Html;
use yii\helpers\Url;

// force reload css to avoid cache
$this->registerCssFile(Yii::getAlias('@web/css/covoit.css?v=' . time())); 

$this->title = 'Mon Profil';
?>

<div class="container py-5">
    
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <?php 
                    // if no photo, use default icon
                    $photo = !empty($user->photo) ? $user->photo : 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; 
                    ?>
                    <img src="<?= Html::encode($photo) ?>" alt="Profil" class="rounded-circle img-fluid shadow">
                </div>
                <div class="col-md-7">
                    <h2 class="mb-1"><?= Html::encode($user->prenom . ' ' . $user->nom) ?></h2>
                    <p class="text-muted mb-1">@<?= Html::encode($user->pseudo) ?> · <?= Html::encode($user->mail) ?></p>
                    <span class="badge <?= $user->isConducteur() ? 'bg-gradient-primary' : 'bg-light' ?>">
                        <?= $user->isConducteur() ? 'Conducteur certifié' : 'Passager' ?>
                    </span>
                </div>
                <div class="col-md-3 text-end">
                    <?= Html::beginForm(['/site/logout'], 'post') ?>
                        <?= Html::submitButton('<i class="fa fa-sign-out-alt me-2"></i> Se déconnecter', ['class' => 'btn btn-outline-danger']) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <h4 class="font-weight-bold mb-3">Mes Réservations</h4>
        <?php if (empty($reservations)): ?>
            <div class="alert alert-light">Aucune réservation. <a href="<?= Url::to(['voyage/index']) ?>">Rechercher</a></div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($reservations as $resa): 
                    // get the details of the trip using the relation
                    $voyage = $resa->voyageObject;
                    $trajet = $voyage ? $voyage->trajetObject : null;
                ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-success">Passager</span>
                                    <span class="text-xs font-weight-bold"><?= $voyage->heuredepart ?>:00</span>
                                </div>
                                <h5 class="mb-1">
                                    <?= Html::encode($trajet ? $trajet->depart : '?') ?> → <?= Html::encode($trajet ? $trajet->arrivee : '?') ?>
                                </h5>
                                <p class="text-sm text-muted">
                                    <?= $resa->nbplaceresa ?> place(s) · Payé: <strong><?= $resa->getTotalPrice() ?> €</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php 
    // show this section only if he is a driver
    if ($user->isConducteur()): ?>
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="font-weight-bold mb-0">Mes Annonces</h4>
                <a href="<?= Url::to(['voyage/create']) ?>" class="btn btn-outline-danger" style="font-size: 0.8rem; padding: 0.4rem 1rem;">
                    <i class="fa fa-plus me-1"></i> Nouveau
                </a>
            </div>

            <?php if (empty($propositions)): ?>
                <div class="alert alert-light">
                    Vous n'avez proposé aucun voyage.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($propositions as $voyage): 
                        $trajet = $voyage->trajetObject;
                    ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-gradient-primary">Conducteur</span>
                                        <span class="text-xs font-weight-bold"><?= $voyage->heuredepart ?>:00</span>
                                    </div>
                                    
                                    <h5 class="mb-1">
                                        <?= Html::encode($trajet ? $trajet->depart : '?') ?> → <?= Html::encode($trajet ? $trajet->arrivee : '?') ?>
                                    </h5>
                                    
                                    <p class="text-sm text-muted mb-3">
                                        <?= $trajet->distance ?> km · <?= $voyage->nbplacedispo ?> places dispo
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center" style="border-top: 1px solid #eee; padding-top: 10px; margin-top: 10px;">
                                        <span class="text-xs font-weight-bold text-muted">
                                            <?= $voyage->getReservedSeats() ?> réservé(s)
                                        </span>
                                        <span class="font-weight-bold" style="color: var(--brand-main);">
                                            <?= $voyage->priceFor(1) ?> € <small class="text-muted font-weight-normal">/pers</small>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>