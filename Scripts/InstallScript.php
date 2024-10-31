<?php

use Symfony\Component\Filesystem\Filesystem;

class InstallScript
{
    public static function installConfig()
    {
        $filePath = __DIR__ . '/../../config/packages/system_check.yaml';

        // Use Symfony's Filesystem component for better error handling
        $filesystem = new Filesystem();

        // Create the directory if it does not exist
        if (!$filesystem->exists(dirname($filePath))) {
            $filesystem->mkdir(dirname($filePath), 0755);
        }

        // Prepare the default configuration content
        $content = <<<YAML
system_check:
    name: 'My Application Name' # Set a meaningful application name
    id: 'some_id'                # You can set this or let it default to null
YAML;

        // Write to the file
        $filesystem->dumpFile($filePath, $content);

        echo "Configuration file created at: $filePath\n";
    }
}