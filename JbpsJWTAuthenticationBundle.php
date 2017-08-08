<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle;

use JubaopenTech\JWTAuthenticationBundle\DependencyInjection\Security\Factory\JWTFactory;
use JubaopenTech\JWTAuthenticationBundle\DependencyInjection\Security\Factory\JWTUserFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * JbpJWtAuthenticationBundle.
 */
class JbpJWTAuthenticationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SecurityExtension $extension */
        $extension = $container->getExtension('security');

        $extension->addUserProviderFactory(new JWTUserFactory());
        $extension->addSecurityListenerFactory(new JWTFactory()); // BC 1.x, to be removed in 3.0
    }
}
