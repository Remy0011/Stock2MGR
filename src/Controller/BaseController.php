<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Classe Controller abstraite de l'application Stock2MGR.
 *
 * Tous les contrôleurs de l'application doivent étendre cette classe.
 * Elle hérite de AbstractController de Symfony et centralise
 * les comportements communs à tous les contrôleurs (rendu avec
 * données globales, réponses JSON, etc.).
 */
abstract class BaseController extends AbstractController
{
    /**
     * Effectue le rendu d'un template Twig en injectant automatiquement
     * les données communes à toutes les pages (nom de l'application, année).
     *
     * @param string   $view       Le chemin du template Twig à rendre
     * @param array    $parameters Les variables à passer au template
     * @param Response|null $response  Objet Response optionnel à personnaliser
     *
     * @return Response La réponse HTTP contenant le HTML généré
     */
    protected function renderView(string $view, array $parameters = [], ?Response $response = null): Response
    {
        // Injection des données globales disponibles dans tous les templates
        $parameters = array_merge([
            'appName' => 'Stock2MGR',
            'currentYear' => date('Y'),
        ], $parameters);

        return $this->render($view, $parameters, $response);
    }

    /**
     * Retourne une réponse JSON standardisée pour les API.
     *
     * @param mixed $data    Les données à sérialiser en JSON
     * @param int   $status  Le code HTTP de la réponse
     * @param array $headers En-têtes HTTP supplémentaires
     *
     * @return Response La réponse HTTP au format JSON
     */
    protected function apiResponse(mixed $data, int $status = Response::HTTP_OK, array $headers = []): Response
    {
        return $this->json($data, $status, $headers);
    }
}
