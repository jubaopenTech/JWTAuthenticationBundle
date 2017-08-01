<?php

/**
 * @author zhaozhuobin
 */

namespace JWTAuthenticationBundle\DependencyInjection\Security\Factory;

use JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Creates the `jbp_jwt` user provider.
 */
final class JWTUserFactory implements UserProviderFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config)
    {
        $container->setParameter('jwt.provider.class',$config['class']);
    }

    public function getKey()
    {
        return 'jbp_jwt';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('class')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($class) {
                            return !(new \ReflectionClass($class))->implementsInterface(JWTUserInterface::class);
                        })
                        ->thenInvalid('The %s class must implement '.JWTUserInterface::class.' for using the "jbp_jwt" user provider.')
                    ->end()
                ->end()
            ->end()
        ;
    }
}
