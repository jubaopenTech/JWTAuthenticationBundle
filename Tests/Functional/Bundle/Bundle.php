<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Functional\Bundle;

use JubaopenTech\JWTAuthenticationBundle\Tests\Functional\Bundle\DependencyInjection\BundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;

class Bundle extends BaseBundle
{
    public function getContainerExtension()
    {
        return new BundleExtension();
    }
}
