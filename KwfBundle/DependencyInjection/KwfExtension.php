<?php
namespace KwfBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class KwfExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('kwf.csrf_protection.ignore_paths', $config['csrf_protection']['ignore_paths']);
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('framework', array(
            'session' => array('storage_id' => 'kwf.session.storage'),
            'validation' => array(
                'enable_annotations' => false
            ),
            'serializer' => array(
                'enable_annotations' => false
            ),
            'templating' => array(
                'engines' => array('twig') //requried for NelmioApiDocBundle
            ),
            'router' => array(
                'strict_requirements' => null
            ),
            'annotations' => array(
                'cache' => 'kwf.annotations.cache'
            )
        ));

        $container->prependExtensionConfig('twig', array(
            'debug' => '%kernel.debug%',
            'strict_variables' => '%kernel.debug%',
        ));

        $container->prependExtensionConfig('sensio_framework_extra', array(
            'view' => array('annotations' => false) //required by fos_rest
        ));

        $container->prependExtensionConfig('fos_rest', array(
            'routing_loader' => array(
                'default_format' => 'json'
            ),
            'view' => array(
                'view_response_listener' => 'force'
            ),
            'param_fetcher_listener' => true
        ));

        $container->prependExtensionConfig('security', array(
            'session_fixation_strategy' => 'none'
        ));
    }
}
