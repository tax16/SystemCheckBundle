<?php

namespace Tax16\SystemCheckBundle\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Installer implements PluginInterface, EventSubscriberInterface
{
    public const PACKAGE_NAME = 'tax16/system-check-bundle'; // Your bundle name
    public const FILES = [
        'config/packages/system_check.yaml',
        'config/packages/system_services.yaml',
    ];

    protected Composer $composer;

    protected IOInterface $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * Attach package installation events.
     *
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => 'postPackageInstall',
            PackageEvents::POST_PACKAGE_UPDATE => 'postPackageUpdate',
            PackageEvents::PRE_PACKAGE_UNINSTALL => 'prePackageUninstall',
        ];
    }

    /**
     * When this package is installed.
     */
    public function postPackageInstall()
    {
        $this->copyConfigFile();
    }

    /**
     * When this package is updated.
     */
    public function postPackageUpdate()
    {
        $this->copyConfigFile();
    }

    /**
     * When this package is uninstalled.
     */
    public function prePackageUninstall()
    {
        $this->removeConfigFile();
    }

    private function copyConfigFile()
    {
        foreach (self::FILES as $file) {
            if (!copy($this->getSourcePath() . $file, $this->getDestPath() . $file)) {
                $this->io->write('<fg=red>An error occurred while copying ' . $file . '</fg=red>');
            }
        }
    }

    private function removeConfigFile()
    {
        foreach (self::FILES as $file) {
            if (!@unlink($this->getDestPath() . $file)) {
                $this->io->write('<fg=red>An error occurred while deleting ' . $file . '</fg=red>');
            }
        }
    }

    private function getSourcePath(): string
    {
        return $this->composer->getConfig()->get('vendor-dir') . '/' . self::PACKAGE_NAME . '/';
    }

    private function getDestPath(): string
    {
        return $this->composer->getConfig()->get('vendor-dir') . '/../';
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // Optionally implement logic on deactivation
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        // Optionally implement logic on uninstall
    }
}
