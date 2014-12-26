<?php

namespace Tahoe\Bundle\MultiTenancyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TahoeMultiTenancyExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter($this->getAlias() . ".account_prefix", $config['account_prefix']);
        // we load the registration subscriber config
        if (array_key_exists('registration_subscriber', $config)) {
            $this->loadRegistrationSubscriber($container, $config['registration_subscriber']);
        } else {
            // load default subscriber
            $this->loadRegistrationSubscriber($container, []);
        }
        // we set the subdomain strategy
        $container->setParameter(sprintf("%s.subdomain_strategy", $this->getAlias()), $config['subdomain_strategy']);
        // we load all the rest of the files
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('controllers.yml');
        $loader->load('factories.yml');
        $loader->load('repositories.yml');
        $loader->load('services.yml');
        $loader->load('handlers.yml');
        $loader->load('subscribers.yml');
        $loader->load('forms.yml');

        if (array_key_exists('gateways', $config)) {
            $loader->load('gateways.yml');
            $this->loadGatewaysParameters($container, $config['gateways']);
        }
    }

    private function loadRegistrationSubscriber(ContainerBuilder $container, array $config)
    {
        $definition = new Definition();
        if (array_key_exists('class', $config)) {
            $definition->setClass($config['class']);
        } else {
            $definition->setClass('Tahoe\Bundle\MultiTenancyBundle\EventSubscriber\RegistrationSubscriber');
        }
        $definition->addTag('kernel.event_subscriber');
        if (array_key_exists('manager', $config)) {
            $definition->addArgument(new Reference($config['manager']));
        } else {
            $definition->addArgument(new Reference('tahoe.multi_tenancy.registration_manager'));
        }
        if (array_key_exists('router', $config)) {
            $definition->addMethodCall('setRouter', array(new Reference($config['router'])));
        } else {
            $definition->addMethodCall('setRouter', array(new Reference('tahoe.multi_tenancy.tenant_aware_router')));
        }

        // we add the definition to the container
        $container->setDefinition('tahoe.multi_tenancy.registration_subscriber', $definition);
    }

    private function loadGatewaysParameters(ContainerInterface $container, $gateways)
    {
        foreach($gateways as $gateway => $params) {
            foreach($params as $key => $paramater) {
                $container->setParameter(sprintf("%s.%s.%s", $this->getAlias(), $gateway, $key), $paramater);;
            }
        }
    }
}
