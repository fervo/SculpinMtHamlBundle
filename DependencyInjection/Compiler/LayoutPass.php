<?php

/*
 * This file is a part of Sculpin.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fervo\Sculpin\Bundle\MtHamlBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class LayoutPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('sculpin_twig.flexible_extension_filesystem_loader')) {
            return;
        }

        $container->findDefinition('sculpin_twig.flexible_extension_filesystem_loader')
            ->setClass('Fervo\Sculpin\Bundle\MtHamlBundle\FlexibleExtensionFilesystemLoader')
            ->addArgument(new Reference('sculpin_mthaml.environment.twig'));
    }
}
