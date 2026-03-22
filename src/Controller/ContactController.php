<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de la page de contact.
 *
 * Gère l'affichage du formulaire de contact de l'application.
 * Étend BaseController pour bénéficier des fonctionnalités communes.
 */
final class ContactController extends BaseController
{
    /**
     * Affiche la page de contact.
     *
     * Route : GET /contact-us
     * Nom   : app_contact
     *
     * @return Response La réponse HTTP contenant la page de contact
     */
    #[Route('/contact-us', name: 'app_contact')]
    public function index(): Response
    {
        // Rendu du template contact sans données supplémentaires
        return $this->renderView('contact/index.html.twig');
    }
}
