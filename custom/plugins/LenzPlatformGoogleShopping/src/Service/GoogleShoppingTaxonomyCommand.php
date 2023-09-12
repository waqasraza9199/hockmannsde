<?php declare(strict_types=1);

namespace Lenz\GoogleShopping\Service;

use Lenz\GoogleShopping\Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GoogleShoppingTaxonomyCommand extends Command
{

    protected static $defaultName = 'lenz:googleshopping:taxonomy:update';
    /**
     * @var Service\GoogleShoppingTaxonomyService
     */
    private $googleShoppingTaxonomyService;

    public function __construct(Service\GoogleShoppingTaxonomyService $googleShoppingTaxonomyService)
    {
        parent::__construct();
        $this->googleShoppingTaxonomyService = $googleShoppingTaxonomyService;
    }

    protected function configure(): void
    {
        $this->setDescription('Update google taxonomy.');
        $this->setHelp('This command helps updating the google category taxonomy.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time = microtime(true);

        $this->googleShoppingTaxonomyService->import();

        $output->writeln('Took ' . ceil(microtime(true) - $time) . ' seconds.');
    }
}
