<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services\TwigExtension;

use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FileExistsExtension extends AbstractExtension
{
    /**
     * @var Packages
     */
    private $assetPackages;

    public function __construct(Packages $assetPackages)
    {
        $this->assetPackages = $assetPackages;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset_exists', [$this, 'assetExists']),
        ];
    }

    public function assetExists(string $path): bool
    {
        $absolutePath = $this->assetPackages->getUrl($path);

        return file_exists($_SERVER['DOCUMENT_ROOT'].$absolutePath);
    }
}
