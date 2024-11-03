<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\UserInterface\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tax16\SystemCheckBundle\Core\Application\Service\HealthCheckHandler;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;

class HealthCheckCommand extends Command
{
    protected static $defaultName = 'system-check:health:check';

    protected static $defaultDescription = 'Execute health check of the application by command line';

    /**
     * @var HealthCheckHandler
     */
    private $healthCheckHandler;

    public function __construct(HealthCheckHandler $healthCheckHandler)
    {
        $this->healthCheckHandler = $healthCheckHandler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription ?? '')
            ->setHelp('This command allows you to execute the health check by command line, --report will give you the json details');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('-------------  BEGIN  ------------');
        $output->writeln('Check system service(s) 😊');

        $result = $this->healthCheckHandler->getHealthCheckDashboard();

        $output->writeln('');

        $successChecks = $result->getSuccessChecks();
        $failedChecks = $result->getFailedChecks();
        $warningChecks = $result->getWarningChecks();

        $output->writeln(
            sprintf('%s Service(s) checked', count($successChecks) + count($failedChecks) + count($warningChecks))
        );

        $output->writeln('');

        $this->createTable($output, 'Success Checks', $successChecks);
        $this->createTable($output, 'Failed Checks', $failedChecks);
        $this->createTable($output, 'Warning Checks', $warningChecks);

        $result = Command::SUCCESS;

        if (count($failedChecks) > 0) {
            $output->writeln('System K.O! 😞');
            $result = Command::FAILURE;
        } else {
            $output->writeln('System O.K! 😊');
        }

        $output->writeln('-------------  FINISH  ------------');

        return $result;
    }

    /**
     * @param array<HealthCheck> $checks
     */
    private function createTable(OutputInterface $output, string $title, array $checks): void
    {
        if (0 === count($checks)) {
            return;
        }

        $table = new Table($output);
        $table
            ->setHeaderTitle($title)
            ->setHeaders(['Label', 'Operation Name']);

        foreach ($checks as $check) {
            $table->addRow([$check->getLabel(), $check->getResult()->getName()]);
        }

        $table->render();
        $output->writeln('');
    }
}
