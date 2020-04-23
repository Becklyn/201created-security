<?php

namespace C201\Security;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-22
 */
class C201SecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $namespaces = ['C201\Security\Infrastructure\Domain\Doctrine'];
        $directories = [realpath(__DIR__ . '/Infrastructure/Store/Doctrine')];
        $managerParameters = [];
        $enabledParameter = false;
        $aliasMap = [];
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createAnnotationMappingDriver(
                $namespaces,
                $directories,
                $managerParameters,
                $enabledParameter,
                $aliasMap
            )
        );
    }
}
