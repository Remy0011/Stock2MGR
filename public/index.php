<?php

/**
 * Front Controller de l'application Stock2MGR.
 *
 * Point d'entrée unique de toutes les requêtes HTTP.
 * Toutes les URL passent par ce fichier grâce à la configuration
 * du serveur web (Apache/Nginx) qui redirige tout vers index.php.
 *
 * Rôle du Front Controller dans le pattern MVC :
 * 1. Charge l'autoloader Composer (PSR-4) pour résoudre les classes
 * 2. Initialise le Kernel Symfony (le cœur de l'application)
 * 3. Le Kernel analyse la requête, détermine la route, appelle le bon
 *    contrôleur, et retourne la réponse HTTP au client
 */

use App\Kernel;

// Chargement de l'autoloader Composer + Runtime Symfony
// Le Runtime gère le cycle requête/réponse automatiquement
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// Fonction de bootstrap : crée et retourne le Kernel de l'application
// Le Runtime Symfony se charge d'exécuter le Kernel avec la requête HTTP courante
return function (array $context) {
    // Instanciation du Kernel avec l'environnement (dev/prod/test) et le mode debug
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
