assets/ —  Ce dossier contient les définitions des bundles d’assets, c’est-à-dire les ensembles de fichiers CSS, JS et images nécessaires à l’affichage du site.

commands/ — Ce répertoire contient les contrôleurs de console, c’est-à-dire des scripts que l’on exécute dans le terminal (et non depuis le navigateur).


config/ — Ce dossier contient tous les fichiers de configuration du framework Yii.
Il définit comment l’application démarre, ses composants (base de données, cache, mail…), ses environnements (web, console, test), et ses paramètres globaux.



controllers/ — Ce dossier contient les contrôleurs web de l’application. Chaque contrôleur relie les actions de l’utilisateur (clics, formulaires, URLs) à la logique de traitement et aux vues à afficher.


mail/layouts/ — Ce dossier contient les layouts utilisés pour les e-mails envoyés par l’application.


models/ — Ce dossier contient les modèles de données et de formulaires de l’application. Les modèles définissent les attributs, les règles de validation et les méthodes pour accéder aux données ou les manipuler.


runtime/ — Dossier temporaire généré automatiquement par Yii lors de l’exécution de l’application.
Il contient les fichiers utilisés pour le debug, le cache, et les logs du système.
	•	debug/ : enregistre les données du Yii Debug Toolbar (requêtes, temps d’exécution, requêtes SQL, erreurs, etc.).
Chaque fichier .data correspond à une requête analysée, et index.data indexe ces enregistrements.
	•	logs/ : contient les fichiers de journalisation comme app.log, où sont notés les événements, messages d’erreur et informations sur l’application.


tests/ — Contient les scripts de tests automatiques de l’application, basés sur Codeception.
	•	unit/ : teste les classes et fonctions individuellement.
	•	functional/ : teste le comportement des contrôleurs.
	•	acceptance/ : teste le site comme un utilisateur réel.
Les dossiers _data, _output, _support gèrent les données, résultats et helpers.
Les fichiers .yml définissent la configuration des suites de tests.



views/ — Ce dossier contient les vues (fichiers PHP/HTML) affichées dans le navigateur.
Chaque sous-dossier correspond à un contrôleur.
	•	layouts/main.php : structure principale du site (en-tête, menu, pied de page). Toutes les pages y sont insérées via la variable $content.
	•	site/ : vues associées au SiteController :
	•	index.php : page d’accueil
	•	about.php : page “À propos”
	•	contact.php : formulaire de contact
	•	login.php : page de connexion
	•	error.php : page d’erreur par défaut


web/ — Racine publique de l’application. Contient le point d’entrée (index.php), les fichiers visibles par le navigateur (CSS, favicon, robots.txt), et les ressources compilées (assets/).
Le sous-dossier css/ gère l’apparence, tandis que .htaccess configure les règles d’accès et les URLs propres.


widgets/ — Contient des composants visuels réutilisables appelés widgets, qui encapsulent une logique et une vue.

Alert.php affiche les messages flash stockés dans la session (succès, erreur, info, etc.) en utilisant les alertes Bootstrap. Il est généralement appelé dans le layout principal pour informer l’utilisateur après certaines actions (connexion, envoi de formulaire, etc.).

