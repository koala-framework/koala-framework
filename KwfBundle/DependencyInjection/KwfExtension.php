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
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('framework', array(
            'session' => array('storage_id' => 'kwf.session.storage'),
            'validation' => null,
            'serializer' => array(
                'enable_annotations' => true
            ),
            'templating' => array(
                'engines' => array('twig') //requried for NelmioApiDocBundle
            ),
            'router' => array(
                'strict_requirements' => null
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
            'providers' => array(
                'kwf_user_provider' => array('id' => 'kwf_user_provider')
            ),
            'firewalls' => array(
                //disables authentication for assets and the profiler, adapt it according to your needs
                'dev' => array(
                    'pattern' => '^/kwf/symfony/(_(profiler|wdt)|css|images|js)/',
                    'security' => false
                ),
                'main' => array(
                    'anonymous' => null,
                    'stateless' => true,
                    'simple_preauth' => array(
                        'authenticator' => 'kwf_authenticator',
                    ),
                    'provider' => 'kwf_user_provider',
                    'entry_point' => 'kwf.security.entrypoint.api',
                )
            )
        ));
    }
}
