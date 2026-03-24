<?php

namespace App\Command;

use App\Service\AlertService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Run: php bin/console stock:check-alerts
 *
 * Can be scheduled via cron:
 *   0 8 * * * /path/to/php /path/to/project/bin/console stock:check-alerts >> /var/log/stock_cron.log 2>&1
 */
#[AsCommand(
    name:        'stock:check-alerts',
    description: 'Scans all products and outputs low-stock / out-of-stock alerts.',
)]
class StockAlertCommand extends Command
{
    public function __construct(
        private readonly AlertService $alertService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $alerts = $this->alertService->scanAll();

        if (empty($alerts)) {
            $io->success('All products are within acceptable stock levels. No alerts.');
            return Command::SUCCESS;
        }

        $io->title(sprintf('⚠  %d Stock Alert(s) Found', count($alerts)));

        $rows = [];
        foreach ($alerts as $alert) {
            $p = $alert['product'];
            $rows[] = [
                strtoupper($alert['level']),
                $p->getName(),
                $p->getSku(),
                $p->getCurrentStock(),
                $p->getAlertThreshold(),
            ];
        }

        $io->table(['Level', 'Product', 'SKU', 'Stock', 'Threshold'], $rows);

        $counts = $this->alertService->countByLevel();
        if ($counts['critical'] > 0) {
            $io->error(sprintf('%d product(s) are OUT OF STOCK.', $counts['critical']));
        }
        if ($counts['warning'] > 0) {
            $io->warning(sprintf('%d product(s) are LOW ON STOCK.', $counts['warning']));
        }

        return Command::FAILURE; // non-zero = alerts exist (useful for cron monitoring)
    }
}
