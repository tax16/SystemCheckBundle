<?php

$finder = (new PhpCsFixer\Finder())
    ->in(['src', 'installer'])
    ->exclude(['var', 'vendor', 'bin'])
;

$config = new PhpCsFixer\Config();
$config
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short']
    ])
    ->setFinder($finder)
;

return $config;