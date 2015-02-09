<?php

namespace Fervo\Sculpin\Bundle\MtHamlBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Sculpin Markdown Bundle.
 *
 * @author Magnus Nordlander <magnus@fervo.se>
 */
class SculpinMtHamlBundle extends Bundle
{
    const CONVERTER_NAME = 'mthaml';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DependencyInjection\Compiler\LayoutPass());
    }
}
