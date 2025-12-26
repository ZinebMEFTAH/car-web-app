<?php

/** @var yii\web\View $this */
/** @var app\models\VoyageForm $model */
/** @var app\models\Voyage[] $voyages */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Recherche de voyage';
?>

<div class="voyage-page-wrapper">

<!-- HERO TEXT + SEARCH BAR -->
<div class="hero-wrapper">

<div class="hero-text text-center">
<h1 class="hero-title">
Trouvez votre prochain covoiturage
</h1>
<p class="hero-subtitle">
Des trajets simples entre villes françaises, en quelques clics.
</p>
</div>

<div class="hero-search-card">
<?php $searchForm = ActiveForm::begin([
'id' => 'form-recherche-voyage',
'action' => ['voyage/rechercher-voyage'],
'method' => 'post',
'options' => ['class' => 'hero-search-form'],
]); ?>

<div class="hero-search-row">

<div class="search-col">
<?php echo $searchForm->field($model, 'depart', [
'options' => ['class' => 'form-group mb-0'],
])->label('Départ')->textInput([
'placeholder' => 'Ville de départ',
'class' => 'form-control search-input',
]); ?>
</div>

<div class="search-col">
<?php echo $searchForm->field($model, 'arrivee', [
'options' => ['class' => 'form-group mb-0'],
])->label('Destination')->textInput([
'placeholder' => 'Ville d\'arrivée',
'class' => 'form-control search-input',
]); ?>
</div>
<div class="search-col search-col-small">
<?php echo $searchForm->field($model, 'nbPersonnes', [
'options' => ['class' => 'form-group mb-0'],
])->label('Passagers')->input('number', [
'min' => 1,
'class' => 'form-control search-input',
'placeholder' => '1',
]); ?>
</div>

<div class="search-col search-col-small d-flex align-items-center">
<?php echo $searchForm->field($model, 'correspondance', [
// Changed 'flex-column' to 'align-items-center' to put Box and Text side-by-side
'options' => ['class' => 'form-group mb-0 d-flex align-items-center'],
])->checkbox([
'value' => 1,
'uncheck' => 0,
'checked' => $model->correspondance == 1,
'class' => 'form-check-input',
// Added margin-right to separate box from text
'style' => 'width: 22px; height: 22px; cursor: pointer; margin-right: 10px; margin-top: 0;',
'label' => 'Correspondance', // The text to display next to the box
'labelOptions' => ['class' => 'mb-0 text-muted font-weight-bold', 'style' => 'font-size: 0.95rem; cursor:pointer;']
])->label(false); // <--- IMPORTANT: Hides the outer label "Corresp."
?>
</div>

<div class="search-col search-col-button">
<div class="form-group mb-0 d-flex align-items-stretch">
<?php echo Html::submitButton('Rechercher', [
'class' => 'btn btn-primary search-btn flex-fill',
]); ?>
</div>
</div>

</div>

<?php ActiveForm::end(); ?>
</div>

</div>

<!-- RÉSULTATS -->
<div id="table-result" class="results-wrapper">
<?php
echo $this->render('table-result', [
'messageNew' => $messageNew ?? true,
]);
?>
</div>

</div>

<div id="modal-reservation" class="custom-modal-overlay">
<div class="custom-modal-box">
<div class="custom-modal-header">
<h3 class="custom-modal-title">Confirmer la réservation</h3>
<button type="button" class="btn-close-modal" id="close-modal-x">&times;</button>
</div>

<div class="modal-body">
<p>Vous êtes sur le point de réserver un trajet :</p>
<div class="modal-info-box">
<div class="info-row">
<span class="info-label">Trajet</span>
<span class="info-value" id="modal-trajet">Avignon → Paris</span>
</div>
<div class="info-row">
<span class="info-label">Départ</span>
<span class="info-value" id="modal-heure">08:00</span>
</div>
<div class="info-row">
<span class="info-label">Passagers</span>
<span class="info-value" id="modal-places">2 personnes</span>
</div>
<div class="info-row total-row">
<span class="info-label">Total à payer</span>
<span class="info-value total-price" id="modal-prix">45.00 €</span>
</div>
</div>

<p style="font-size: 0.8rem; color: #888; text-align: center;">
En cliquant sur confirmer, vous acceptez les conditions générales.
</p>
</div>

<div class="custom-modal-footer">
<button type="button" class="btn-cancel" id="btn-cancel-modal">Annuler</button>
<?php $formResa = \yii\widgets\ActiveForm::begin([
'action' => ['reservation/creer'],
'method' => 'post',
'id' => 'form-confirm-reservation'
]); ?>
<input type="hidden" name="voyage_id" id="input-voyage-id">
<input type="hidden" name="nb_places" id="input-nb-places">
<button type="submit" class="btn-confirm">Confirmer et Payer</button>
<?php \yii\widgets\ActiveForm::end(); ?>
</div>
</div>
</div>

<?php


$this->registerJsFile(
'@web/js/voyage.js',
['depends' => [\yii\web\JqueryAsset::class]]
);
?>


