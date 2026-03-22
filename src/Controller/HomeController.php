<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de la page d'accueil.
 *
 * Gère l'affichage de la page principale de l'application Stock2MGR.
 * Étend BaseController pour bénéficier des fonctionnalités communes.
 */
final class HomeController extends BaseController
{
    /**
     * Affiche la page d'accueil.
     *
     * Route : GET /
     * Nom   : app_home
     *
     * @return Response La réponse HTTP contenant la page d'accueil
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Rendu du template avec la variable classroom passée à la vue
        return $this->renderView('home/index.html.twig', [
            'classroom' => 'BSD',
        ]);
    }
}
