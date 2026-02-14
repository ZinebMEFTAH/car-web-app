<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\widgets\Alert;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\helpers\Url;

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= Html::encode($this->title) ?></title>
    <base href="<?= Yii::$app->request->baseUrl ?>/">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web') ?>/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="<?= Yii::getAlias('@web') ?>/css/covoit.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>

<nav class="navbar navbar-main navbar-expand-lg px-3 mb-0 shadow-none border-radius-xl" id="navbarBlur" data-scroll="false">
        <div class="container-fluid py-1 px-3">

        <div class="navbar-brand-row">
            <nav aria-label="breadcrumb">
                <h6 class="font-weight-bolder mb-0 d-flex align-items-center">
                    <img src="<?= Yii::getAlias('@web/img/logo.png') ?>" alt="Logo" class="brand-logo">
                    <a href="<?= Url::to(['site/index']) ?>" 
                       class="text-dark text-decoration-none"
                       >
                        Covoit‘Voyages
                    </a>
                </h6>
            </nav>

            <button class="navbar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggle-line"></span>
                <span class="navbar-toggle-line"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse justify-content-end" id="navbar">
            
            <ul class="navbar-nav d-flex flex-column flex-lg-row align-items-lg-center gap-3 mt-3 mt-lg-0">

                <li class="nav-item">
                    <a href="<?= Url::to(['voyage/index']) ?>" class="nav-link text-body px-2">
                        Rechercher
                    </a>
                </li>

                <?php if (!Yii::$app->user->isGuest && !empty(Yii::$app->user->identity->permis)): ?>
                    <li class="nav-item">
                        <a href="<?= Url::to(['voyage/create']) ?>" class="nav-link text-body px-2 font-weight-bold">
                            Proposer un voyage
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->isGuest): ?>
                    <li class="nav-item">
                        <a href="<?= Url::to(['site/login']) ?>" class="btn btn-sm btn-outline-dark mb-0 ms-lg-2">
                            Se connecter
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="<?= Url::to(['site/profile']) ?>" class="nav-link text-body font-weight-bold px-0 ms-lg-2" >
                            Mon Profil
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<main class="main-content position-relative border-radius-lg">
    <div class="container-fluid py-4" >
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <div class="mb-2">
                <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
            </div>
        <?php endif ?>
        
        <div id="notif"></div>

        <div id="content-container">
            <?= $content ?>
        </div>

        <footer class="footer pt-4 mt-4">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    
                    <div class="text-muted text-sm">
                        Covoit‘Voyages — © 2025
                    </div>

                </div>
            </div>
        </footer>
    </div>
</main>

<?php
$this->registerJsFile('@web/js/bootstrap.bundle.min.js', ['position' => \yii\web\View::POS_END]);
// avoid cache on js too
$this->registerJsFile('@web/js/voyage.js?v=' . time(), ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/js/navbar.js?v=' . time(), ['depends' => [\yii\web\JqueryAsset::class]]);

?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>