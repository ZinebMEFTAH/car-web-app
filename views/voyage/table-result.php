<?php

/** @var app\models\VoyageForm $model */
/** @var app\models\Voyage[] $voyages (DIRECT) */
/** @var array $paths (CORRESPONDENCE) */
/** @var bool $messageNew */

use yii\helpers\Html;

// FIX: Define this at the top so it is available everywhere
$nbDemandes = (isset($model) && $model->nbPersonnes) ? (int)$model->nbPersonnes : 1; 
?>

<?php if ((!empty($voyages) || !empty($paths)) && isset($model)) : ?>
    <div class="results-header-line">
        <span class="results-header-title">Résultats de la recherche</span>
        <span class="results-header-sub">
            <?php echo Html::encode($model->depart); ?> → <?php echo Html::encode($model->arrivee); ?>
            · <?php echo $nbDemandes; ?> passager(s)
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
            $trajet = $voyage->trajetObject;
            if (!$trajet) continue; 

            $placesRestantes = $voyage->getRemainSeats();
            // $nbDemandes is already defined at top

            $isComplet = ($placesRestantes < $nbDemandes);
            $hasEnough = ($placesRestantes >= $nbDemandes);

            if ($isComplet) {
                $subPlacesText = 'aucune place disponible';
                $placesText = 'Complet';
                $placesTextClass = 'vc-seats-low';
            } elseif ($hasEnough) {
                $subPlacesText = "pour {$nbDemandes} passager(s)";
                $placesText = $placesRestantes . ' ' . (($placesRestantes > 1) ? 'places disponibles' : 'place disponible');
                $placesTextClass = 'vc-state-free';
            } else {
                $subPlacesText = "pour {$placesRestantes} place(s) disponible(s)";
                $placesText = $placesRestantes . ' ' . (($placesRestantes > 1) ? 'places insuffisantes' : 'place insuffisante');
                $placesTextClass = 'vc-seats-low';
            }

            $prixParPassager = $voyage->priceFor(1);
            $nbPourCalcul = $hasEnough ? $nbDemandes : max($placesRestantes, 0);
            $totalCalcule = $nbPourCalcul > 0 ? $voyage->priceFor($nbPourCalcul) : 0;
            $duree        = $voyage->getDuree();
            $heureArrivee = $voyage->getHeureArrivee();
            $driver       = $voyage->conducteurObject ?? null;
            ?>

            <article class="voyage-card">
                <div class="voyage-card-top">
                    <div class="vc-main-info">
                        <div class="vc-cities <?php echo $isComplet ? 'vc-cities-red' : ''; ?>">
                            <div class="vc-city-from"><?php echo Html::encode($trajet->depart); ?></div>
                            <div class="vc-city-to"><?php echo Html::encode($trajet->arrivee); ?></div>
                        </div>
                        <div class="vc-extra">
                            <span class="vc-extra-label">Places restantes :</span>
                            <span class="vc-places-text <?php echo $placesTextClass; ?>">
                                <?php echo Html::encode($placesText); ?>
                            </span>
                        </div>
                    </div>

                    <div class="vc-times vc-times-centered">
                        <div class="vc-time-block">
                            <span class="vc-time"><?php echo Html::encode(sprintf('%02d:00', (int) $voyage->heuredepart)); ?></span>
                            <span class="vc-time-label">Départ</span>
                        </div>
                        <div class="vc-duration-block">
                            <div class="vc-duration-bar"></div>
                            <div class="vc-duration-text"><?php echo Html::encode($duree); ?></div>
                        </div>
                        <div class="vc-time-block">
                            <span class="vc-time"><?php echo Html::encode($heureArrivee); ?></span>
                            <span class="vc-time-label">Arrivée</span>
                        </div>
                    </div>

                    <div class="vc-price<?php echo $isComplet ? ' vc-price-complete' : (!$hasEnough ? ' vc-price-partial' : ''); ?>">
                        <div class="vc-price-text">
                            <div class="vc-price-main"><?php echo Html::encode(number_format($totalCalcule, 2, ',', ' ')); ?> €</div>
                            <div class="vc-price-sub"><?php echo Html::encode(number_format($prixParPassager, 2, ',', ' ')); ?> € / passager · <?php echo Html::encode($subPlacesText); ?></div>
                        </div>
                        <div class="vc-price-action">
                            <?php if ($isComplet): ?>
                                <button class="vc-reserve-btn vc-reserve-btn-disabled" disabled>Complet</button>
                            <?php else: ?>
                                <button type="button" class="vc-reserve-btn btn-open-modal"
                                        data-id="<?= $voyage->id ?>"
                                        data-depart="<?= Html::encode($trajet->depart) ?>"
                                        data-arrivee="<?= Html::encode($trajet->arrivee) ?>"
                                        data-prix="<?= $totalCalcule ?>"
                                        data-places="<?= $nbDemandes ?>"
                                        data-heure="<?= Html::encode($voyage->heuredepart) ?>:00">
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
                                <?php echo $driver ? Html::encode(trim($driver->prenom . ' ' . $driver->nom)) : 'Conducteur non renseigné'; ?>
                            </div>
                            <div class="vc-driver-note">
                                <?php echo !empty($voyage->contraintes) ? 'Contraintes : ' . Html::encode($voyage->contraintes) : 'Aucune contrainte particulière.'; ?>
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
            $steps = count($path);
            if ($steps < 2) continue;

            $first = $path[0];
            $last  = $path[$steps - 1];
            
            // Safe Object Check
            if (!$first->trajetObject || !$last->trajetObject) continue;

            // Calculate Totals safely
            $totalPrix = 0;
            foreach ($path as $v) { 
                if ($v->trajetObject) {
                    $totalPrix += $v->priceFor($nbDemandes); 
                }
            }

            $heureDepartGlobal = sprintf('%02d:00', (int) $first->heuredepart);
            
            // Safe Duration Logic
            $dist = isset($last->trajetObject->distance) ? $last->trajetObject->distance : 60;
            
            $lastDurationH = floor($dist / 60); 
            $lastDurationM = $dist % 60;
            
            $finalHourRaw  = $last->heuredepart + ($dist / 60);
            $finalHour     = floor($finalHourRaw);
            $finalMin      = round(($finalHourRaw - $finalHour) * 60);
            $heureArriveeGlobal = sprintf('%02d:%02d', $finalHour % 24, $finalMin);
        ?>

        <article class="voyage-card" style="border-left: 5px solid #18a394;">
            
            <div class="voyage-card-top">
                <div class="vc-main-info">
                    <div class="vc-cities">
                        <div class="vc-city-from"><?php echo Html::encode($first->trajetObject->depart); ?></div>
                        <div class="vc-city-to" style="color: #0d7667;"><?php echo Html::encode($last->trajetObject->arrivee); ?></div>
                    </div>
                    <div class="vc-extra">
                        <span class="vc-extra-label badge bg-warning text-dark">Correspondance</span>
                        <span class="vc-places-text text-muted"><?= $steps ?> étapes</span>
                    </div>
                </div>

                <div class="vc-times vc-times-centered">
                    <div class="vc-time-block">
                        <span class="vc-time"><?= $heureDepartGlobal ?></span>
                        <span class="vc-time-label">Départ</span>
                    </div>
                    
                    <div class="vc-duration-block" style="min-width: 100px; text-align: center;">
                        <small class="text-muted" style="font-size: 0.75rem;">via</small>
                        <div style="font-size: 0.85rem; font-weight: 600;">
                            <?php 
                                for($i=0; $i<$steps-1; $i++) {
                                    if ($path[$i]->trajetObject) {
                                        echo Html::encode($path[$i]->trajetObject->arrivee);
                                        if ($i < $steps-2) echo ', ';
                                    }
                                }
                            ?>
                        </div>
                    </div>

                    <div class="vc-time-block">
                        <span class="vc-time"><?= $heureArriveeGlobal ?></span>
                        <span class="vc-time-label">Arrivée</span>
                    </div>
                </div>

                <div class="vc-price">
                    <div class="vc-price-text">
                        <div class="vc-price-main"><?php echo number_format($totalPrix, 2, ',', ' '); ?> €</div>
                        <div class="vc-price-sub">Total pour <?php echo $nbDemandes; ?> pers.</div>
                    </div>
                    <div class="vc-price-action">
                        <button class="vc-reserve-btn vc-reserve-btn-disabled" 
                                style="background: #e2e8f0; color: #64748b; cursor: not-allowed;" disabled>
                            Complexe
                        </button>
                    </div>
                </div>
            </div>

            <div class="voyage-card-bottom" style="background: #f8fafc; margin: 0 -1.6rem -0.9rem; padding: 10px 1.6rem;">
                <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Détails du trajet :</small>
                <div class="d-flex flex-column mt-2 gap-2">
                    <?php foreach ($path as $idx => $leg): ?>
                        <?php if ($leg->trajetObject): ?>
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
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

        </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (empty($voyages) && empty($paths) && !$messageNew) : ?>
    <p class="results-placeholder">
        Aucun voyage trouvé pour ces critères.
    </p>
<?php elseif (empty($voyages) && empty($paths) && $messageNew) : ?>
    <p class="results-placeholder">
        Aucune recherche n'a encore été effectuée.
    </p>
<?php endif; ?>