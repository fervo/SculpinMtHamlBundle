<?php

namespace Fervo\Sculpin\Bundle\MtHamlBundle;

use MtHaml\Environment;
use MtHaml\Support\Twig\Loader as HamlLoader;
use Sculpin\Bundle\TwigBundle\FlexibleExtensionFilesystemLoader as BaseLoader;

class FlexibleExtensionFilesystemLoader extends BaseLoader
{
    /**
     * Constructor.
     *
     * @param string   $sourceDir
     * @param string[] $sourcePaths
     * @param string[] $paths
     * @param string[] $extensions
     */
    public function __construct($sourceDir, array $sourcePaths, array $paths, array $extensions, Environment $environment)
    {
        parent::__construct($sourceDir, $sourcePaths, $paths, $extensions);

        $this->filesystemLoader = new HamlLoader($environment, $this->filesystemLoader);
    }
}
