<?php
namespace KwfBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class UpdatesProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('kwf.updates_provider_locator')) {
            return;
        }

        $definition = $container->findDefinition(
            'kwf.updates_provider_locator'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'kwf.updates_provider'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addUpdateProvider',
                array(new Reference($id))
            );
        }
    }
}
