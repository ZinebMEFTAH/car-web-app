<?php
/** @var \app\models\Internaute $user */

use yii\helpers\Html;
use app\models\Internaute;
use app\models\Reservation;
use app\models\Voyage;
use app\models\Trajet;
?>

<h1>Test Internaute : <?= Html::encode($user->pseudo) ?></h1>

<h2>Infos utilisateur</h2>
<ul>
    <li>ID : <?= $user->id ?></li>
    <li>Pseudo : <?= Html::encode($user->pseudo) ?></li>
    <li>Nom : <?= Html::encode($user->nom) ?></li>
    <li>Prénom : <?= Html::encode($user->prenom) ?></li>
    <li>Nom complet (getFullName) : <?= Html::encode($user->FullName) ?></li>
    <li>Email : <?= Html::encode($user->mail) ?></li>
    <li>Photo : <?= Html::encode($user->photo) ?></li>
    <li>Permis : <?= $user->permis ?: 'Aucun (pas conducteur)' ?></li>
    <li>Est conducteur ? <?= $user->isConducteur() ? 'Oui' : 'Non' ?></li>
    <li>Nombre de réservations (getNombreReservations) : <?= $user->NombreReservations ?></li>
    <li>Nombre de voyages conduits (getNombreVoyagesConduits) : <?= $user->NombreVoyagesConduits ?></li>
</ul>

<hr>

<h2>Voyages qu'il conduit</h2>
<?php if ($user->voyagesConduitsObject): ?>
    <ul>
        <?php foreach ($user->voyagesConduitsObject as $v): ?>
            <li>
                <strong>Voyage #<?= $v->id ?></strong><br>

                <u>Champs bruts voyage :</u><br>
                • Conducteur ID : <?= $v->conducteur ?><br>
                • Trajet ID : <?= $v->trajet ?><br>
                • Type véhicule ID : <?= $v->idtypev ?><br>
                • Marque ID : <?= $v->idmarquev ?><br>
                • Tarif (€/km/personne) : <?= $v->tarif ?><br>
                • Places disponibles : <?= $v->nbplacedispo ?><br>
                • Bagages : <?= $v->nbbagage ?><br>
                • Heure départ : <?= $v->heuredepart ?><br>
                • Contraintes : <?= Html::encode($v->contraintes) ?><br>

                <br><u>Trajet (getTrajetObject, getWritten, hasVoyages, getNombreVoyages) :</u><br>
                <?php if ($v->trajetObject): ?>
                    <?php $trajet = $v->trajetObject; ?>
                    • Départ : <?= Html::encode($trajet->depart) ?><br>
                    • Arrivée : <?= Html::encode($trajet->arrivee) ?><br>
                    • Distance : <?= $trajet->distance ?> km<br>
                    • Written : <?= Html::encode($trajet->written) ?><br>
                    • Trajet a des voyages ? <?= $trajet->hasVoyages() ? 'Oui' : 'Non' ?><br>
                    • Nombre de voyages pour ce trajet : <?= $trajet->nombreVoyages ?><br>
                <?php else: ?>
                    (trajet inconnu)<br>
                <?php endif; ?>

                <br><u>Type véhicule (getTypeVehiculeObject) :</u><br>
                <?php if ($v->typeVehiculeObject): ?>
                    • Libellé : <?= Html::encode($v->typeVehiculeObject->typev) ?><br>
                <?php else: ?>
                    (type véhicule inconnu)<br>
                <?php endif; ?>

                <br><u>Marque (getMarqueVehiculeObject) :</u><br>
                <?php if ($v->marqueVehiculeObject): ?>
                    • Nom marque : <?= Html::encode($v->marqueVehiculeObject->marquev) ?><br>
                <?php else: ?>
                    (marque inconnue)<br>
                <?php endif; ?>

                <br><u>Réservations liées (getReservationsObject, hasReservations, getNombreReservations, getReservedSeats, getRemainSeats, isComplete, canItAccept, priceFor, isReservedBy) :</u><br>
                • A des réservations ? <?= $v->hasReservations() ? 'Oui' : 'Non' ?><br>
                • Nombre de réservations : <?= $v->nombreReservations ?><br>
                • Places déjà réservées : <?= $v->reservedSeats ?><br>
                • Places restantes : <?= $v->remainSeats ?><br>
                • Voyage complet ? <?= $v->isComplete() ? 'Oui' : 'Non' ?><br>
                • Peut accepter 1 place ? <?= $v->canItAccept(1) ? 'Oui' : 'Non' ?><br>
                • Peut accepter 3 places ? <?= $v->canItAccept(3) ? 'Oui' : 'Non' ?><br>
                • Prix pour 1 place : <?= $v->priceFor(1) ?> €<br>
                • Prix pour 2 places : <?= $v->priceFor(2) ?> €<br>
                • Ce voyage est-il réservé par cet internaute ?
                  <?= $v->isReservedBy($user) ? 'Oui' : 'Non' ?><br>

                <hr>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Aucun voyage conduit.</p>
<?php endif; ?>

<hr>

<h2>Réservations qu'il a faites</h2>
<?php if ($user->reservationsObject): ?>
    <ul>
        <?php foreach ($user->reservationsObject as $r): ?>
            <li>
                <strong>Réservation #<?= $r->id ?></strong><br>

                <u>Champs bruts réservation :</u><br>
                • Voyage réservé ID : <?= $r->voyage ?><br>
                • Voyageur ID : <?= $r->voyageur ?><br>
                • Nombre de places : <?= $r->nbplaceresa ?><br>

                <br><u>Relation voyageur (getVoyageurObject / isReservationForInternaute) :</u><br>
                <?php if ($r->voyageurObject): ?>
                    • Voyageur pseudo : <?= Html::encode($r->voyageurObject->pseudo) ?><br>
                    • Est-ce une réservation pour cet internaute ?
                      <?= $r->isReservationForInternaute($user) ? 'Oui' : 'Non' ?><br>
                <?php else: ?>
                    (voyageur introuvable)<br>
                <?php endif; ?>

                <br><u>Détails du voyage lié (getVoyageObject, isReservationOfVoyage) :</u><br>
                <?php if ($r->voyageObject): ?>
                    <?php $voy = $r->voyageObject; ?>
                    • Voyage ID : <?= $voy->id ?><br>
                    • Conducteur ID : <?= $voy->conducteur ?><br>
                    • Tarif (€/km/personne) : <?= $voy->tarif ?><br>
                    • Places dispo : <?= $voy->nbplacedispo ?><br>
                    • Places restantes : <?= $voy->remainSeats ?><br>
                    • Réservation liée à ce voyage ?
                      <?= $r->isReservationOfVoyage($voy) ? 'Oui' : 'Non' ?><br>

                    <?php if ($voy->trajetObject): ?>
                        <br>• Trajet :
                        <?= Html::encode($voy->trajetObject->depart) ?>
                        → <?= Html::encode($voy->trajetObject->arrivee) ?>
                        (<?= $voy->trajetObject->distance ?> km)<br>
                    <?php endif; ?>
                <?php else: ?>
                    (voyage introuvable)<br>
                <?php endif; ?>

                <br><u>Prix total (getTotalPrice) :</u><br>
                • Prix total : <?= $r->totalPrice ?> €<br>

                <hr>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Aucune réservation.</p>
<?php endif; ?>

<hr>

<h2>Tests rapides des fonctions statiques</h2>
<ul>
    <li>
        Internaute::getUserByIdentifiant(<?= Html::encode($user->pseudo) ?>) :
        <?php $u2 = Internaute::getUserByIdentifiant($user->pseudo); ?>
        <?= $u2 ? 'OK, ID = ' . $u2->id : 'Aucun' ?>
    </li>

    <?php
    $oneReservation = $user->reservationsObject ? $user->reservationsObject[0] : null;
    $oneVoyage = $user->voyagesConduitsObject ? $user->voyagesConduitsObject[0] : null;
    ?>

    <?php if ($oneReservation && $oneReservation->voyageObject): ?>
        <li>
            Reservation::getReservationsByVoyageId(<?= $oneReservation->voyage ?>) :
            <?php
            $listRes = Reservation::getReservationsByVoyageId($oneReservation->voyage);
            ?>
            <?= count($listRes) ?> réservation(s) trouvée(s)
        </li>
    <?php endif; ?>

    <?php if ($oneVoyage && $oneVoyage->trajetObject): ?>
        <li>
            Trajet::getTrajet(<?= Html::encode($oneVoyage->trajetObject->depart) ?>,
            <?= Html::encode($oneVoyage->trajetObject->arrivee) ?>) :
            <?php
            $t2 = Trajet::getTrajet($oneVoyage->trajetObject->depart, $oneVoyage->trajetObject->arrivee);
            ?>
            <?= $t2 ? 'OK, ID = ' . $t2->id : 'Aucun' ?>
        </li>

        <li>
            Voyage::getVoyagesByTrajetId(<?= $oneVoyage->trajet ?>) :
            <?php
            $voyList = Voyage::getVoyagesByTrajetId($oneVoyage->trajet);
            ?>
            <?= count($voyList) ?> voyage(s) trouvé(s)
        </li>
    <?php endif; ?>
</ul>