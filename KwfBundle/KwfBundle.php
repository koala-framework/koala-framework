<?php
namespace KwfBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use KwfBundle\DependencyInjection\MaintenanceJobsCompilerPass;
use KwfBundle\DependencyInjection\UpdatesProviderCompilerPass;

class KwfBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->setParameter('kernel.secret', \Kwf_Util_Hash::getPrivatePart());

        $container->setParameter('kernel.framework.session.storage_id', 'session.storage.kwf');
        $container->setParameter('framework.session.storage_id', 'session.storage.kwf');
        $container->setParameter('session.storage_id', 'session.storage.kwf');
        $container->setParameter('session', null);
        $container->setParameter('kernel.session', null);

        $container->set('kernel.framework.session.storage_id', 'session.storage.kwf');
        $container->set('framework.session.storage_id', 'session.storage.kwf');
        $container->set('session.storage_id', 'session.storage.kwf');

        $container->setParameter('kernel.framework.session.storage', 'session.storage.kwf');
        $container->setParameter('framework.session.storage', 'session.storage.kwf');
        $container->setParameter('session.storage', 'session.storage.kwf');

        $container->set('kernel.framework.session.storage', 'session.storage.kwf');
        $container->set('framework.session.storage', 'session.storage.kwf');
        $container->set('session.storage', 'session.storage.kwf');

        $container->addCompilerPass(new MaintenanceJobsCompilerPass());
        $container->addCompilerPass(new UpdatesProviderCompilerPass());
    }
}
