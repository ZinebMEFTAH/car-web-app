<?php

/** @var app\models\VoyageForm $model */
/** @var app\models\Voyage[] $voyages (DIRECT) */
/** @var array $paths (CORRESPONDENCE) */
/** @var bool $messageNew */

use yii\helpers\Html;

// global variable for quantity
$nbDemandes = (isset($model) && $model->nbPersonnes) ? (int)$model->nbPersonnes : 1; 
?>

<?php if ((!empty($voyages) || !empty($paths)) && isset($model)) : ?>
    <div class="results-header-line">
        <span class="results-header-title">Résultats de la recherche</span>
        <span class="results-header-sub">
            <?= Html::encode($model->depart) ?> → <?= Html::encode($model->arrivee) ?>
            · <?= $nbDemandes ?> passager(s)
        </span>
    </div>
<?php endif; ?>

<?php if (!empty($voyages)) : ?>
    <div class="results-list">
        <?php if (!empty($paths)): ?>
            <h5 class="mb-3 text-success" style="font-weight: 700; padding-left: 10px;">
                <i class="fa fa-check-circle me-2"></i>Trajets Directs
            </h5>
        <?php endif; ?>

        <?php foreach ($voyages as $voyage) : ?>
            <?php
            // safety check
            if (!$voyage->trajetObject) continue; 

            // we calculate the remaining seats only once
            $remains = $voyage->getRemainSeats();
            $isFull  = ($remains < $nbDemandes);
            
            // calculate price based on availability
            $calcPrice = $isFull ? 0 : $voyage->priceFor($nbDemandes);
            ?>

            <article class="voyage-card">
                <div class="voyage-card-top">
                    <div class="vc-main-info">
                        <div class="vc-cities <?= $isFull ? 'vc-cities-red' : '' ?>">
                            <div class="vc-city-from"><?= Html::encode($voyage->trajetObject->depart) ?></div>
                            <div class="vc-city-to"><?= Html::encode($voyage->trajetObject->arrivee) ?></div>
                        </div>
                        <div class="vc-extra">
                            <span class="vc-extra-label">Places restantes :</span>
                            <span class="vc-places-text <?= ($remains >= $nbDemandes) ? 'vc-state-free' : 'vc-seats-low' ?>">
                                <?= $isFull ? 'Complet' : $remains . ' place(s) disponible(s)' ?>
                            </span>
                        </div>
                    </div>

                    <div class="vc-times vc-times-centered">
                        <div class="vc-time-block">
                            <span class="vc-time"><?= sprintf('%02d:00', $voyage->heuredepart) ?></span>
                            <span class="vc-time-label">Départ</span>
                        </div>
                        <div class="vc-duration-block">
                            <div class="vc-duration-bar"></div>
                            <div class="vc-duration-text"><?= Html::encode($voyage->getDuree()) ?></div>
                        </div>
                        <div class="vc-time-block">
                            <span class="vc-time"><?= Html::encode($voyage->getHeureArrivee()) ?></span>
                            <span class="vc-time-label">Arrivée</span>
                        </div>
                    </div>

                    <div class="vc-price<?= $isFull ? ' vc-price-complete' : '' ?>">
                        <div class="vc-price-text">
                            <div class="vc-price-main"><?= number_format($calcPrice, 2, ',', ' ') ?> €</div>
                            <div class="vc-price-sub">
                                <?= number_format($voyage->priceFor(1), 2, ',', ' ') ?> € / passager
                            </div>
                        </div>
                        <div class="vc-price-action">
                            <?php if ($isFull): ?>
                                <button class="vc-reserve-btn vc-reserve-btn-disabled" disabled>Complet</button>
                            <?php else: ?>
                                <button type="button" class="vc-reserve-btn btn-open-modal"
                                        data-id="<?= $voyage->id ?>"
                                        data-depart="<?= Html::encode($voyage->trajetObject->depart) ?>"
                                        data-arrivee="<?= Html::encode($voyage->trajetObject->arrivee) ?>"
                                        data-prix="<?= $calcPrice ?>"
                                        data-places="<?= $nbDemandes ?>"
                                        data-heure="<?= $voyage->heuredepart ?>:00">
                                    Réserver
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="voyage-card-bottom">
                    <div class="vc-driver">
                        <div class="vc-avatar-placeholder"></div>
                        <div class="vc-driver-text">
                            <div class="vc-driver-name">
                                <?= $voyage->conducteurObject ? Html::encode(trim($voyage->conducteurObject->prenom . ' ' . $voyage->conducteurObject->nom)) : 'Inconnu' ?>
                            </div>
                            <div class="vc-driver-note">
                                <?= !empty($voyage->contraintes) ? 'Contraintes : ' . Html::encode($voyage->contraintes) : 'Aucune contrainte.' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($paths)) : ?>
    
    <div class="results-list">
        <h5 class="mt-5 mb-3 text-primary" style="font-weight: 700; padding-left: 10px;">
            <i class="fa fa-route me-2"></i>Itinéraires avec correspondance
        </h5>

        <?php foreach ($paths as $path) : 
            $count = count($path);
            if ($count < 2) continue;

            // shortcuts for array access
            $first = $path[0];
            $last  = $path[$count - 1];
            
            if (!$first->trajetObject || !$last->trajetObject) continue;

            // loop for total price and ID string only
            $totalPrix = 0;
            $ids = [];
            foreach ($path as $v) { 
                $totalPrix += $v->priceFor($nbDemandes); 
                $ids[] = $v->id;
            }

            // quick calc for arrival
            $dist = $last->trajetObject->distance ?? 60;
            $finalRaw = $last->heuredepart + ($dist / 60);
            $hArr = sprintf('%02d:%02d', floor($finalRaw) % 24, round(($finalRaw - floor($finalRaw)) * 60));
        ?>

        <article class="voyage-card" style="border-left: 5px solid #18a394;">
            
            <div class="voyage-card-top">
                <div class="vc-main-info">
                    <div class="vc-cities">
                        <div class="vc-city-from"><?= Html::encode($first->trajetObject->depart) ?></div>
                        <div class="vc-city-to" style="color: #0d7667;"><?= Html::encode($last->trajetObject->arrivee) ?></div>
                    </div>
                    <div class="vc-extra">
                        <span class="vc-extra-label badge bg-warning text-dark">Correspondance</span>
                        <span class="vc-places-text text-muted"><?= $count ?> étapes</span>
                    </div>
                </div>

                <div class="vc-times vc-times-centered">
                    <div class="vc-time-block">
                        <span class="vc-time"><?= sprintf('%02d:00', $first->heuredepart) ?></span>
                        <span class="vc-time-label">Départ</span>
                    </div>
                    
                    <div class="vc-duration-block" style="min-width: 100px; text-align: center;">
                        <small class="text-muted" style="font-size: 0.75rem;">via</small>
                        <div style="font-size: 0.85rem; font-weight: 600;">
                            <?php 
                                // show intermediate cities
                                for($i=0; $i<$count-1; $i++) {
                                    echo Html::encode($path[$i]->trajetObject->arrivee);
                                    if ($i < $count-2) echo ', ';
                                }
                            ?>
                        </div>
                    </div>

                    <div class="vc-time-block">
                        <span class="vc-time"><?= $hArr ?></span>
                        <span class="vc-time-label">Arrivée</span>
                    </div>
                </div>

                <div class="vc-price-action" style="display: flex; justify-content: flex-end; width: 100%;">
                    <button type="button" class="vc-reserve-btn btn-open-modal"
                            data-id="<?= implode(',', $ids) ?>"
                            data-depart="<?= Html::encode($first->trajetObject->depart) ?>"
                            data-arrivee="<?= Html::encode($last->trajetObject->arrivee) ?>"
                            data-prix="<?= $totalPrix ?>"
                            data-places="<?= $nbDemandes ?>"
                            data-heure="<?= sprintf('%02d:00', $first->heuredepart) ?>">
                        Réserver
                    </button>
                </div>
            </div>

            <div class="voyage-card-bottom" style="background: #f8fafc; margin: 0 -1.6rem -0.9rem; padding: 10px 1.6rem;">
                <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Détails du trajet :</small>
                <div class="d-flex flex-column mt-2 gap-2">
                    <?php foreach ($path as $leg): ?>
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-4" style="font-size: 0.85rem;">
                            <span>
                                <strong><?= $leg->heuredepart ?>h00</strong> : 
                                <?= Html::encode($leg->trajetObject->depart) ?> 
                                <i class="fa fa-arrow-right mx-1 text-muted"></i> 
                                <?= Html::encode($leg->trajetObject->arrivee) ?>
                            </span>
                            <span class="badge bg-white border text-dark">
                                <?= number_format($leg->priceFor($nbDemandes), 2) ?> €
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (empty($voyages) && empty($paths) && !$messageNew) : ?>
    <p class="results-placeholder">Aucun voyage trouvé pour ces critères.</p>
<?php elseif (empty($voyages) && empty($paths) && $messageNew) : ?>
    <p class="results-placeholder">Aucune recherche n'a encore été effectuée.</p>
<?php endif; ?>