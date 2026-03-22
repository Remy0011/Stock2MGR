<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de gestion des stocks.
 *
 * Gère l'affichage de la liste des produits en stock.
 * C'est le contrôleur principal du domaine métier de l'application Stock2MGR.
 * Étend BaseController pour bénéficier des fonctionnalités communes.
 */
#[Route('/stock', name: 'app_stock_')]
final class StockController extends BaseController
{
    /**
     * Affiche la liste des produits en stock.
     *
     * Route : GET /stock
     * Nom   : app_stock_index
     *
     * @return Response La réponse HTTP contenant la liste des stocks
     */
    #[Route('', name: 'index')]
    public function index(): Response
    {
        // Données statiques en attendant la couche Model (Entity/Repository)
        $stocks = [
            ['id' => 1, 'name' => 'Clavier mécanique', 'quantity' => 45, 'price' => 79.99],
            ['id' => 2, 'name' => 'Souris sans fil',    'quantity' => 120, 'price' => 29.99],
            ['id' => 3, 'name' => 'Écran 27 pouces',    'quantity' => 8,  'price' => 349.00],
        ];

        // Rendu de la vue avec la liste des produits
        return $this->renderView('stock/index.html.twig', [
            'stocks' => $stocks,
        ]);
    }
}
