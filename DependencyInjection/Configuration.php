<?php

namespace Tahoe\Bundle\MultiTenancyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tahoe\Bundle\MultiTenancyBundle\Service\TenantResolver;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tahoe_multi_tenancy');

        $supportedStrategies = [
            TenantResolver::STRATEGY_TENANT_AWARE_SUBDOMAIN,
            TenantResolver::STRATEGY_FIXED_SUBDOMAIN
        ];

        $rootNode
            ->children()
                ->scalarNode('subdomain_strategy')
                    ->isRequired()
                    ->validate()
                        ->ifNotInArray($supportedStrategies)
                        ->thenInvalid('The subdomain %s is not supported. Please provide one of ' . json_encode($supportedStrategies))
                    ->end()
                ->end()
                ->scalarNode('account_prefix')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('registration_subscriber')
                    ->children()
                        ->scalarNode('class')->cannotBeEmpty()->defaultValue('Tahoe\Bundle\MultiTenancyBundle\EventSubscriber\RegistrationSubscriber')->end()
                        ->scalarNode('manager')->end()
                        ->scalarNode('router')->end()
                        ->scalarNode('redirect_route')->end()
                    ->end()
                ->end()
//                ->scalarNode('registration_subscriber')->cannotBeEmpty()->end()
                ->arrayNode('gateways')
                    ->children()
                        ->arrayNode('recurly')
                            ->children()
                                ->scalarNode('subdomain')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('private_key')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('plan_name')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
