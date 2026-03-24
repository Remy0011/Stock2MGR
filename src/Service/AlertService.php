<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Entity\StockMovement;
use App\Repository\ProductRepository;
use App\Repository\StockMovementRepository;
use App\Service\AlertService;
use App\Service\StockService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StockServiceTest extends TestCase
{
    private StockService $service;

    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $em;

    /** @var StockMovementRepository&MockObject */
    private StockMovementRepository $movementRepo;

    /** @var AlertService&MockObject */
    private AlertService $alertService;

    protected function setUp(): void
    {
        $this->em           = $this->createMock(EntityManagerInterface::class);
        $this->movementRepo = $this->createMock(StockMovementRepository::class);
        $productRepo        = $this->createMock(ProductRepository::class);
        $this->alertService = $this->createMock(AlertService::class);

        $this->service = new StockService(
            $this->em,
            $productRepo,
            $this->movementRepo,
            $this->alertService,
        );
    }

    // ── addStock ──────────────────────────────────────────────────────────

    public function testAddStockIncreasesCurrentStock(): void
    {
        $product = $this->makeProduct(10);

        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $movement = $this->service->addStock($product, 5, 10.00, 'PO-001');

        $this->assertSame(15, $product->getCurrentStock());
        $this->assertSame(StockMovement::TYPE_IN, $movement->getType());
        $this->assertSame(5, $movement->getRemainingFifoQty());
        $this->assertSame(10.00, $movement->getUnitCost());
    }

    public function testAddStockRejectsNonPositiveQuantity(): void
    {
        $this->expectException(\DomainException::class);
        $this->service->addStock($this->makeProduct(0), 0);
    }

    // ── removeStock ───────────────────────────────────────────────────────

    public function testRemoveStockDecreasesCurrentStock(): void
    {
        $product = $this->makeProduct(10);

        // One FIFO layer with 10 remaining
        $layer = $this->makeFifoLayer(10, 10.00);
        $this->movementRepo->method('findAvailableFifoLayers')->willReturn([$layer]);

        $this->em->method('persist');
        $this->em->method('flush');
        $this->alertService->method('checkAndNotify');

        $movement = $this->service->removeStock($product, 4, 'SO-001');

        $this->assertSame(6, $product->getCurrentStock());
        $this->assertSame(StockMovement::TYPE_OUT, $movement->getType());
        $this->assertSame(6, $layer->getRemainingFifoQty()); // FIFO consumed 4
    }

    public function testRemoveStockThrowsWhenInsufficientStock(): void
    {
        $product = $this->makeProduct(3);
        $this->movementRepo->method('findAvailableFifoLayers')->willReturn([]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Insufficient stock');

        $this->service->removeStock($product, 10);
    }

    // ── FIFO consumption ──────────────────────────────────────────────────

    public function testFifoConsumesOldestLayerFirst(): void
    {
        $product = $this->makeProduct(15);

        $layer1 = $this->makeFifoLayer(10, 5.00); // oldest
        $layer2 = $this->makeFifoLayer(5,  7.00);

        $this->movementRepo->method('findAvailableFifoLayers')->willReturn([$layer1, $layer2]);
        $this->em->method('persist');
        $this->em->method('flush');
        $this->alertService->method('checkAndNotify');

        $this->service->removeStock($product, 12);

        // layer1 fully consumed (was 10, now 0)
        $this->assertSame(0, $layer1->getRemainingFifoQty());
        // layer2 partially consumed (was 5, consumed 2, remaining 3)
        $this->assertSame(3, $layer2->getRemainingFifoQty());
        $this->assertSame(3, $product->getCurrentStock());
    }

    // ── adjustStock ───────────────────────────────────────────────────────

    public function testPositiveAdjustmentAddsStock(): void
    {
        $product = $this->makeProduct(5);
        $this->movementRepo->method('findAvailableFifoLayers')->willReturn([]);
        $this->em->method('persist');
        $this->em->method('flush');
        $this->alertService->method('checkAndNotify');

        $this->service->adjustStock($product, +3, 'ADJ-001');

        $this->assertSame(8, $product->getCurrentStock());
    }

    public function testNegativeAdjustmentRemovesStock(): void
    {
        $product = $this->makeProduct(10);
        $layer   = $this->makeFifoLayer(10, 5.00);
        $this->movementRepo->method('findAvailableFifoLayers')->willReturn([$layer]);
        $this->em->method('persist');
        $this->em->method('flush');
        $this->alertService->method('checkAndNotify');

        $this->service->adjustStock($product, -4);

        $this->assertSame(6, $product->getCurrentStock());
    }

    public function testAdjustmentCannotBringStockBelowZero(): void
    {
        $this->expectException(\DomainException::class);
        $this->service->adjustStock($this->makeProduct(3), -10);
    }

    public function testZeroDeltaAdjustmentThrows(): void
    {
        $this->expectException(\DomainException::class);
        $this->service->adjustStock($this->makeProduct(5), 0);
    }

    // ── getFifoAverageCost ────────────────────────────────────────────────

    public function testAverageCostIsWeighted(): void
    {
        $product = $this->makeProduct(15);

        // 10 units @ 5 € + 5 units @ 8 € → avg = (50+40)/15 = 6.00 €
        $l1 = $this->makeFifoLayer(10, 5.00);
        $l2 = $this->makeFifoLayer(5,  8.00);
        $this->movementRepo->method('findAvailableFifoLayers')->willReturn([$l1, $l2]);

        $avg = $this->service->getFifoAverageCost($product);

        $this->assertEqualsWithDelta(6.00, $avg, 0.001);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function makeProduct(int $stock): Product
    {
        $p = new Product();
        $p->setName('Test')->setSku('TST-001')->setUnitPrice(10.0)->setCurrentStock($stock)->setAlertThreshold(0);
        return $p;
    }

    private function makeFifoLayer(int $remaining, float $unitCost): StockMovement
    {
        $m = new StockMovement();
        $m->setType(StockMovement::TYPE_IN)
            ->setQuantity($remaining)
            ->setRemainingFifoQty($remaining)
            ->setUnitCost($unitCost);
        return $m;
    }
}
