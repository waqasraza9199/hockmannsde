<?php declare(strict_types = 1);

namespace ZaibaSandMailToValidOrders\Command\OrderSubscriber;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use ZaibaSandMailToValidOrders\ScheduledTasks\Handlers\OrderSubscriber\OrderTaskHandler;

class Notify extends Command
{
    protected $taskHandler;

    public function __construct(
        OrderTaskHandler $taskHandler,
        $name = null
    )
    {
        $this->taskHandler = $taskHandler;

        parent::__construct($name);
    }


    protected function configure(): void
    {
        $this->setName('zaiba:sand-mail-to-valid-order:notify');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $verbosity = $output->getVerbosity();
        if ($verbosity >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln('<comment>Running ZaibaSandMailToValidOrders OrderSubscriber OrderTask...</comment>');
        }

        try {
            $this->taskHandler->run($output, $verbosity);

            if ($verbosity >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln('<info>ZaibaSandMailToValidOrders OrderSubscriber OrderTask finished.</info>');
            }

            return 0;
        } catch (\Throwable $e) {
            if ($verbosity >= OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln('<error>ZaibaSandMailToValidOrders OrderSubscriber OrderTask failed.</error>');
                $output->writeln('<error>' . $e->getMessage() . '</error>');
            }

            return 1;
        }
    }
}
