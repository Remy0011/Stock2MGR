<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Kernel de l'application Stock2MGR.
 *
 * Le Kernel est le cœur du framework Symfony et orchestre le pattern MVC :
 * - Charge la configuration (bundles, services, routes)
 * - Reçoit les requêtes HTTP du Front Controller
 * - Résout la route vers le bon contrôleur (Controller)
 * - Le contrôleur interagit avec le modèle (Model) et retourne une vue (View)
 *
 * Le MicroKernelTrait simplifie la configuration en chargeant
 * automatiquement les fichiers depuis le dossier config/.
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
