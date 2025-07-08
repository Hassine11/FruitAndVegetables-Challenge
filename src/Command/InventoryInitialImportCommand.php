<?php

namespace App\Command;

use App\Inventory\Domain\ItemName;
use App\Inventory\Domain\Unit;
use App\Inventory\Domain\Weight;
use App\Inventory\Factory\FruitFactory;
use App\Inventory\Factory\VegetableFactory;
use App\Repository\InventoryRepository;
use Symfony\Bundle\FrameworkBundle\Command\AbstractConfigCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Example : bin/console app:import:inventory.
 */
#[AsCommand('app:import:inventory', 'Import inventory')]
class InventoryInitialImportCommand extends AbstractConfigCommand
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly InventoryRepository $inventoryRepository,
    ) {
        parent::__construct('inventory:import');
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info> Start Importing Inventory</info>');
        try {
            $importFilePath = $this->kernel->getProjectDir().'/request.json';
            $requestData = json_decode(file_get_contents($importFilePath), true);
        } catch (\Exception $exception) {
            $output->writeln('<error>Error While Reading ImportFile'.$exception->getMessage().'</error>');

            return Command::FAILURE;
        }

        $output->writeln('<info> Start Storing Inventory Items</info>');
        foreach ($requestData as $item) {
            try {
                $article = match ($item['type']) {
                    'fruit' => (new FruitFactory())->create(
                        ItemName::fromString($item['name']),
                        Weight::fromUnitAndQuantity($item['unit'], $item['quantity']),
                        Unit::fromString(Unit::GRAM),
                    ),
                    'vegetable' => (new VegetableFactory())->create(
                        ItemName::fromString($item['name']),
                        Weight::fromUnitAndQuantity($item['unit'], $item['quantity']),
                        Unit::fromString(Unit::GRAM),
                    ),
                };

                $this->inventoryRepository->storeInventoryArticle($article);
            } catch (\Exception $e) {
                $output->writeln('<error>Error While Importing Inventory : '.$e->getMessage().'</error>');

                return Command::FAILURE;
            }
        }

        $output->writeln('<info>Successfully Imported Inventory Articles</info>');

        return Command::SUCCESS;
    }
}
