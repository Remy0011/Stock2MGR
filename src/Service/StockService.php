<?php

namespace App\Command;

use App\Repository\ProductRepository;
use App\Service\StockService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Run: php bin/console stock:fifo-report [sku]
 *
 * Shows active FIFO layers for one or all products.
 */
#[AsCommand(
    name:        'stock:fifo-report',
    description: 'Displays active FIFO stock layers and weighted-average cost.',
)]
class FifoReportCommand extends Command
{
    public function __construct(
        private readonly ProductRepository $productRepo,
        private readonly StockService      $stockService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('sku', InputArgument::OPTIONAL, 'Filter by product SKU');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io  = new SymfonyStyle($input, $output);
        $sku = $input->getArgument('sku');

        $products = $sku
            ? array_filter($this->productRepo->findAll(), fn($p) => $p->getSku() === $sku)
            : $this->productRepo->findAll();

        if (empty($products)) {
            $io->error($sku ? "No product found with SKU: $sku" : 'No products found.');
            return Command::FAILURE;
        }

        foreach ($products as $product) {
            $layers  = $this->stockService->getFifoLayers($product);
            $avgCost = $this->stockService->getFifoAverageCost($product);

            $io->section(sprintf('%s  [%s]  stock: %d', $product->getName(), $product->getSku(), $product->getCurrentStock()));

            if (empty($layers)) {
                $io->text('  No FIFO layers available.');
                continue;
            }

            $rows = [];
            foreach ($layers as $layer) {
                $rows[] = [
                    $layer['date']->format('d/m/Y H:i'),
                    $layer['qty'],
                    $layer['remaining'],
                    $layer['unitCost'] !== null ? number_format($layer['unitCost'], 2) . ' €' : '—',
                ];
            }

            $io->table(['Date IN', 'Qty IN', 'Remaining', 'Unit Cost'], $rows);
            $io->text(sprintf('  Weighted avg. cost: <info>%.2f €</info>', $avgCost));
        }

        return Command::SUCCESS;
    }
}
