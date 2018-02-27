<?php
namespace KwfBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MaintenanceJobsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('kwf.maintenance_jobs_locator')) {
            return;
        }

        $definition = $container->findDefinition(
            'kwf.maintenance_jobs_locator'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'kwf.maintenance_job'
        );
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addMaintenanceJobServiceId',
                array($id)
            );
        }
    }
}
