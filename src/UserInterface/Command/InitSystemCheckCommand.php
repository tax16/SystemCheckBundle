<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\UserInterface\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class InitSystemCheckCommand extends Command
{
    protected static $defaultName = 'system-check:init';

    private const ASSETS_SOURCE = __DIR__ . '/../../UserInterface/Resources/public/';
    private const ASSETS_DESTINATION = '/public/bundles/systemcheck';
    private const CONFIG_SOURCE = __DIR__ . '/../../Resources/config/packages/';
    private const CONFIG_DESTINATION = '/config/packages/';
    private const CONFIG_FILES = [
        'system_check.yaml',
        'system_services.yaml',
    ];

    protected function configure(): void
    {
        $this->setDescription('Copy assets and dependencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();
        $projectDir = $this->getApplication()->getKernel()->getProjectDir();

        $this->copyAssets($filesystem, $output, $projectDir);

        $this->copyConfigFiles($filesystem, $output, $projectDir);

        return Command::SUCCESS;
    }

    private function copyAssets(Filesystem $filesystem, OutputInterface $output, string $projectDir): void
    {
        $destination = $projectDir . self::ASSETS_DESTINATION;

        try {
            $output->writeln('<info>Copying assets...</info>');

            if ($filesystem->exists($destination)) {
                $filesystem->remove($destination);
            }

            $filesystem->mirror(self::ASSETS_SOURCE, $destination);
            $output->writeln('<info>Assets copied successfully.</info>');
        } catch (IOExceptionInterface $exception) {
            $output->writeln('<error>Error copying assets: ' . $exception->getPath() . '</error>');
        }
    }

    private function copyConfigFiles(Filesystem $filesystem, OutputInterface $output, string $projectDir): void
    {
        $destinationConfig = $projectDir . self::CONFIG_DESTINATION;

        try {
            $output->writeln('<info>Copying configuration files...</info>');

            foreach (self::CONFIG_FILES as $file) {
                $sourceFile = self::CONFIG_SOURCE . $file;
                $destinationFile = $destinationConfig . $file;

                if ($filesystem->exists($destinationFile)) {
                    $output->writeln("<info>File '$file' exists, skipping copy.</info>");
                    continue;
                }

                $filesystem->copy($sourceFile, $destinationFile);
                $output->writeln("<info>Copied $file to $destinationConfig</info>");
            }
            $output->writeln('<info>Configuration files copied successfully.</info>');
        } catch (IOExceptionInterface $exception) {
            $output->writeln('<error>Error copying configuration files: ' . $exception->getPath() . '</error>');
        }
    }
}